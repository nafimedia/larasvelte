<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\CmsForm;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FormBuilderController extends Controller
{
    public function index()
    {
        $forms = CmsForm::withCount('submissions')->latest()->get();

        if ($forms->isEmpty()) {
            $defaultForm = CmsForm::create([
                'name' => 'Formulir Kontak Standard',
                'slug' => 'contact-us',
                'description' => 'Formulir untuk menerima pertanyaan dan pesan dari pengunjung website.',
                'submit_button_text' => 'Kirim Pesan',
                'fields' => [
                    ['label' => 'Nama Lengkap', 'name' => 'name', 'type' => 'text', 'required' => true],
                    ['label' => 'Alamat Email', 'name' => 'email', 'type' => 'email', 'required' => true],
                    ['label' => 'Subjek Pesan', 'name' => 'subject', 'type' => 'text', 'required' => true],
                    ['label' => 'Isi Pesan', 'name' => 'message', 'type' => 'textarea', 'required' => true],
                ],
            ]);
            $forms = CmsForm::withCount('submissions')->latest()->get();
        }

        return Inertia::render('Admin/Cms/Forms/Index', [
            'forms' => $forms,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array',
            'submit_button_text' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . time();

        CmsForm::create($validated);

        return redirect()->back()->with('success', 'Formulir baru berhasil dibuat');
    }

    public function submissions($id)
    {
        $form = CmsForm::findOrFail($id);
        $submissions = FormSubmission::where('cms_form_id', $id)->latest()->get();

        return Inertia::render('Admin/Cms/Forms/Submissions', [
            'form' => $form,
            'submissions' => $submissions,
        ]);
    }

    public function submitPublicForm(Request $request, $slug)
    {
        $form = CmsForm::where('slug', $slug)->where('is_active', true)->firstOrFail();

        FormSubmission::create([
            'cms_form_id' => $form->id,
            'data' => $request->except(['_token']),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->back()->with('success', 'Terima kasih, pesan Anda berhasil dikirim.');
    }
}
