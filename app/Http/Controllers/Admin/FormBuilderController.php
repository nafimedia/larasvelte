<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormBuilderController extends Controller
{
    /**
     * Display forms dashboard list.
     */
    public function index(): Response
    {
        $forms = Form::withCount('submissions')
            ->with('fields')
            ->latest()
            ->get();

        return Inertia::render('Admin/CMS/Forms/Index', [
            'forms' => $forms,
        ]);
    }

    /**
     * Store new form and redirect to builder studio.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'theme_color' => 'nullable|string|max:30',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Form::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $form = Form::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'theme_color' => $validated['theme_color'] ?? '#6366F1',
            'created_by' => $request->user()->id,
        ]);

        // Add a default first text question
        FormField::create([
            'form_id' => $form->id,
            'type' => 'text',
            'label' => 'Pertanyaan Tanpa Judul',
            'is_required' => false,
            'order' => 1,
        ]);

        activity('forms')
            ->causedBy($request->user())
            ->log("Membuat Formulir Baru: {$form->title}");

        return redirect()->route('admin.cms.forms.builder', $form->id)->with('success', 'Formulir berhasil dibuat!');
    }

    /**
     * Display Google Forms-style Builder Studio (3 Tabs).
     */
    public function builder(int $id): Response
    {
        $form = Form::with(['fields' => function ($query) {
            $query->orderBy('order', 'asc');
        }, 'submissions' => function ($query) {
            $query->latest();
        }])->findOrFail($id);

        return Inertia::render('Admin/CMS/Forms/Builder', [
            'form' => $form,
        ]);
    }

    /**
     * Update form metadata & fields array (Sync fields).
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $form = Form::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:forms,slug,' . $id,
            'description' => 'nullable|string',
            'theme_color' => 'nullable|string|max:30',
            'is_accepting_responses' => 'boolean',
            'confirmation_message' => 'required|string',
            'require_login' => 'boolean',
            'fields' => 'nullable|array',
            'fields.*.id' => 'nullable|integer',
            'fields.*.type' => 'required|string',
            'fields.*.label' => 'required|string',
            'fields.*.help_text' => 'nullable|string',
            'fields.*.placeholder' => 'nullable|string',
            'fields.*.options' => 'nullable|array',
            'fields.*.is_required' => 'boolean',
            'fields.*.order' => 'integer',
        ]);

        $form->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'description' => $validated['description'] ?? null,
            'theme_color' => $validated['theme_color'] ?? '#6366F1',
            'is_accepting_responses' => $validated['is_accepting_responses'] ?? true,
            'confirmation_message' => $validated['confirmation_message'],
            'require_login' => $validated['require_login'] ?? false,
        ]);

        // Sync fields
        $inputFieldIds = [];
        if (! empty($validated['fields'])) {
            foreach ($validated['fields'] as $index => $fieldData) {
                $fieldId = $fieldData['id'] ?? null;
                $fieldPayload = [
                    'form_id' => $form->id,
                    'type' => $fieldData['type'],
                    'label' => $fieldData['label'],
                    'help_text' => $fieldData['help_text'] ?? null,
                    'placeholder' => $fieldData['placeholder'] ?? null,
                    'options' => $fieldData['options'] ?? null,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'order' => $index + 1,
                ];

                if ($fieldId && FormField::where('id', $fieldId)->where('form_id', $form->id)->exists()) {
                    $field = FormField::find($fieldId);
                    $field->update($fieldPayload);
                    $inputFieldIds[] = $field->id;
                } else {
                    $newField = FormField::create($fieldPayload);
                    $inputFieldIds[] = $newField->id;
                }
            }
        }

        // Delete removed fields
        FormField::where('form_id', $form->id)
            ->whereNotIn('id', $inputFieldIds)
            ->delete();

        activity('forms')
            ->causedBy($request->user())
            ->log("Memperbarui Formulir Studio: {$form->title}");

        return back()->with('success', 'Formulir berhasil disimpan!');
    }

    /**
     * Delete form.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $form = Form::findOrFail($id);
        $title = $form->title;

        $form->delete();

        activity('forms')
            ->causedBy($request->user())
            ->log("Menghapus Formulir: {$title}");

        return redirect()->route('admin.cms.forms.index')->with('success', "Formulir '{$title}' berhasil dihapus!");
    }

    /**
     * Toggle accepting responses status.
     */
    public function toggleResponses(Request $request, int $id): RedirectResponse
    {
        $form = Form::findOrFail($id);
        $form->update([
            'is_accepting_responses' => ! $form->is_accepting_responses,
        ]);

        $status = $form->is_accepting_responses ? 'dibuka' : 'ditutup';

        return back()->with('success', "Penerimaan tanggapan formulir '{$form->title}' berhasil {$status}!");
    }

    /**
     * Export form responses as CSV download.
     */
    public function exportCsv(int $id): StreamedResponse
    {
        $form = Form::with(['fields' => function ($q) {
            $q->orderBy('order', 'asc');
        }, 'submissions'])->findOrFail($id);

        $filename = "responses_" . Str::slug($form->title) . "_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($form) {
            $file = fopen('php://output', 'w');

            // Header Row
            $headerRow = ['ID', 'Waktu Kirim', 'IP Address'];
            foreach ($form->fields as $field) {
                $headerRow[] = $field->label;
            }
            fputcsv($file, $headerRow);

            // Submissions Rows
            foreach ($form->submissions as $sub) {
                $row = [
                    $sub->id,
                    $sub->created_at->format('Y-m-d H:i:s'),
                    $sub->ip_address ?? '-',
                ];

                foreach ($form->fields as $field) {
                    $val = $sub->response_data["field_{$field->id}"] ?? null;
                    if (is_array($val)) {
                        $val = implode(', ', $val);
                    }
                    $row[] = $val ?? '-';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
