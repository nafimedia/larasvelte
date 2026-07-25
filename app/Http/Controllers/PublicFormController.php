<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicFormController extends Controller
{
    /**
     * Show public Google Forms response page.
     */
    public function show(string $slug): Response
    {
        $form = Form::with(['fields' => function ($q) {
            $q->orderBy('order', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        return Inertia::render('Public/FormShow', [
            'form' => $form,
        ]);
    }

    /**
     * Submit public form answers.
     */
    public function submit(Request $request, string $slug): RedirectResponse
    {
        $form = Form::with('fields')->where('slug', $slug)->firstOrFail();

        if (! $form->is_accepting_responses) {
            return back()->with('error', 'Formulir ini sudah tidak menerima tanggapan.');
        }

        if ($form->require_login && ! auth()->check()) {
            return back()->with('error', 'Anda harus login terlebih dahulu untuk mengisi formulir ini.');
        }

        // Build dynamic validation rules based on form fields
        $rules = [];
        foreach ($form->fields as $field) {
            $fieldKey = "field_{$field->id}";
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->type === 'file') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'max:5120'; // 5MB max
            } elseif ($field->type === 'checkboxes') {
                $fieldRules[] = 'array';
            }

            $rules[$fieldKey] = implode('|', $fieldRules);
        }

        $validated = $request->validate($rules);

        // Process response data & files
        $responseData = [];
        foreach ($form->fields as $field) {
            $fieldKey = "field_{$field->id}";
            if ($field->type === 'file' && $request->hasFile($fieldKey)) {
                $path = $request->file($fieldKey)->store('form_uploads', 'public');
                $responseData[$fieldKey] = [
                    'path' => $path,
                    'name' => $request->file($fieldKey)->getClientOriginalName(),
                    'url' => \Illuminate\Support\Facades\Storage::disk('public')->url($path),
                ];
            } else {
                $responseData[$fieldKey] = $request->input($fieldKey);
            }
        }

        FormSubmission::create([
            'form_id' => $form->id,
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'response_data' => $responseData,
        ]);

        return back()->with('success_submission', $form->confirmation_message);
    }
}
