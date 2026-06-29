<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use App\Models\Workreport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function actAndUpload(UploadedFile $file): \Illuminate\Testing\TestResponse
    {
        $user = User::create([
            'username' => 'tester', 'email' => 'tester@example.com',
            'name' => 'Tester', 'password' => bcrypt('secret'),
        ]);
        $workreport = Workreport::create([]);

        return $this->actingAs($user)->post('/upload', [
            'files' => $file,
            'aid'   => $workreport->id,
            'uid'   => 1,
            'type'  => 'lieferschein',
        ]);
    }

    public function test_normal_pdf_upload_is_unchanged(): void
    {
        Storage::fake('local');

        $this->actAndUpload(UploadedFile::fake()->create('Lieferschein_123.pdf', 10, 'application/pdf'));

        // Dokument wird mit Originalnamen angelegt (keine sichtbare Aenderung)
        $doc = Document::first();
        $this->assertNotNull($doc, 'Normaler PDF-Upload muss ein Document anlegen');
        $this->assertSame('Lieferschein_123.pdf', $doc->name);
        $this->assertSame('Lieferschein_123.pdf', $doc->path);
        Storage::disk('local')->assertExists('public/upload/Lieferschein_123.pdf');
    }

    public function test_path_traversal_filename_is_sanitized(): void
    {
        Storage::fake('local');

        $this->actAndUpload(UploadedFile::fake()->create('../../../../evil.pdf', 5, 'application/pdf'));

        $doc = Document::first();
        $this->assertNotNull($doc);
        $this->assertSame('evil.pdf', $doc->name, 'Pfadanteile muessen entfernt sein');
        Storage::disk('local')->assertExists('public/upload/evil.pdf');
    }

    public function test_executable_php_upload_is_rejected(): void
    {
        Storage::fake('local');

        $this->actAndUpload(UploadedFile::fake()->create('shell.php', 5, 'application/x-php'));

        $this->assertNull(Document::first(), 'PHP-Upload darf kein Document anlegen');
        Storage::disk('local')->assertMissing('public/upload/shell.php');
    }

    public function test_html_upload_is_rejected(): void
    {
        Storage::fake('local');

        $this->actAndUpload(UploadedFile::fake()->create('xss.html', 5, 'text/html'));

        $this->assertNull(Document::first(), 'HTML-Upload (Stored-XSS) darf kein Document anlegen');
    }
}
