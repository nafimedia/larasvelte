# Implementation Plan - Google Forms-style Dynamic Form Builder System

This feature designs a comprehensive **Google Forms-style Dynamic Form Builder** for the CMS. Administrators can create, edit, customize, and analyze forms using an intuitive 3-tab studio (*Questions Studio, Responses & Analytics, Settings*). End users can fill out forms via public URLs (`/f/{slug}`) or embedded pages.

---

## User Review Required

> [!IMPORTANT]
> - **8 Input Field Types Supported**: Short Answer, Paragraph, Multiple Choice (Radio), Checkboxes, Dropdown, File Upload, Date/Time, and Linear Scale Rating (1-5 / 1-10).
> - **Public Form Routes**: Public form submissions are served at `/f/{slug}` with responsive Google Forms aesthetic.
> - **Response Analytics**: Automatic visual breakdown (pie charts/bar charts) for choice questions and CSV export for raw submission data.

---

## Proposed Changes

### Database Schema & Models

#### [NEW] [2026_07_25_000006_create_forms_table.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/database/migrations/2026_07_25_000006_create_forms_table.php)
Create `forms`, `form_fields`, and `form_submissions` tables:
- `forms`: `title`, `slug`, `description`, `header_image`, `theme_color`, `is_accepting_responses`, `confirmation_message`, `require_login`, `created_by`.
- `form_fields`: `form_id`, `type`, `label`, `help_text`, `placeholder`, `options` (JSON), `is_required`, `order`.
- `form_submissions`: `form_id`, `user_id`, `ip_address`, `user_agent`, `response_data` (JSON), `is_read`.

#### [NEW] [Form.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/app/Models/Form.php)
#### [NEW] [FormField.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/app/Models/FormField.php)
#### [NEW] [FormSubmission.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/app/Models/FormSubmission.php)
Create Eloquent models with relationships (`fields`, `submissions`, `user`, `form`).

#### [NEW] [FormSeeder.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/database/seeders/FormSeeder.php)
Seed sample forms (e.g. *Formulir Kontak Kami*, *Survei Kepuasan Pelanggan*).

---

### Backend Controllers & Middleware

#### [MODIFY] [FormBuilderController.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/app/Http/Controllers/Admin/FormBuilderController.php)
Enhance `FormBuilderController`:
- `index()`: Display list of forms with response counts.
- `create()`: Render Form Builder Studio.
- `store()`: Save form metadata and fields array.
- `builder(int $id)`: Display 3-tab Google Forms Builder Studio (Questions, Responses, Settings).
- `update(Request $request, int $id)`: Update form metadata & sync fields.
- `destroy(int $id)`: Delete form and submissions.
- `toggleResponses(int $id)`: Toggle `is_accepting_responses`.
- `exportCsv(int $id)`: Stream CSV download of all responses.

#### [NEW] [PublicFormController.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/app/Http/Controllers/PublicFormController.php)
- `show(string $slug)`: Render public Google Forms-style submission page.
- `submit(Request $request, string $slug)`: Validate dynamic fields, store uploaded files, and record submission.

#### [MODIFY] [routes/web.php](file:///c:/Users/user/Documents/laragon/www/larasvelte/routes/web.php)
- Register Admin Form Builder routes under `admin/cms/forms`.
- Register Public Form routes under `/f/{slug}` and `/f/{slug}/submit`.

---

### Frontend Svelte Components & Views

#### [MODIFY] [types.ts](file:///c:/Users/user/Documents/laragon/www/larasvelte/resources/js/lib/types.ts)
Add `Form`, `FormFieldItem`, and `FormSubmissionItem` interfaces.

#### [NEW] [Index.svelte](file:///c:/Users/user/Documents/laragon/www/larasvelte/resources/js/Pages/Admin/CMS/Forms/Index.svelte)
Form management dashboard displaying form cards, response counts, share links, and action buttons.

#### [NEW] [Builder.svelte](file:///c:/Users/user/Documents/laragon/www/larasvelte/resources/js/Pages/Admin/CMS/Forms/Builder.svelte)
Google Forms-style 3-tab Builder Studio:
- **Tab 1 (Pertanyaan)**: Question Card editor with drag reordering, floating toolbar, theme color picker, and 8 input field types.
- **Tab 2 (Respon & Tanggapan)**: Response analytics, pie/bar charts, individual submissions viewer, and CSV export.
- **Tab 3 (Pengaturan)**: Confirmation message, login requirement, share URL, and iframe embed code generator.

#### [NEW] [FormShow.svelte](file:///c:/Users/user/Documents/laragon/www/larasvelte/resources/js/Pages/Public/FormShow.svelte)
Clean, modern public Google Forms-style responsive form submission view.

---

## Verification Plan

### Automated Tests
- Run `php artisan db:seed --class=FormSeeder` to populate sample forms.
- Run `npm run build` to verify Svelte and TypeScript compilation.

### Manual Verification
1. Access `/admin/cms/forms` in Admin Dashboard.
2. Click **"Buat Formulir Baru"** -> Form Builder Studio opens in Google Forms layout.
3. Add questions (Short Answer, Multiple Choice, Rating, File Upload) -> Reorder cards using drag buttons.
4. Save form -> Copy public URL `/f/form-slug`.
5. Open `/f/form-slug` in a new tab -> Submit test answers.
6. Return to Admin Builder Studio -> Open **Tab 2 (Respon)** -> Verify submission appears in Analytics & Submissions list.
