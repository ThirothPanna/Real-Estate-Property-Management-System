<?php

namespace App\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Throwable;

class StoredDocumentPreview
{
    public function response(string $path, string $name, ?string $mimeType, string $downloadUrl): Response|\Symfony\Component\HttpFoundation\StreamedResponse
    {
        $storage = Storage::disk('public');
        abort_unless($storage->exists($path), 404, 'File not found on disk.');

        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ];

        if ($extension === 'docx') {
            try {
                $word = IOFactory::load($storage->path($path));
                $writer = IOFactory::createWriter($word, 'HTML');
                ob_start();
                $writer->save('php://output');
                $html = ob_get_clean();
            } catch (Throwable) {
                abort(415, 'This DOCX file could not be previewed. Download it to open in a word processor.');
            }

            return response()->view('documents.preview', [
                'title' => $name,
                'content' => $html,
                'downloadUrl' => $downloadUrl,
            ], 200, $headers + [
                'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; frame-src 'self'; base-uri 'none'",
            ]);
        }

        if ($extension === 'doc') {
            return response()->view('documents.unsupported-preview', [
                'title' => $name,
                'downloadUrl' => $downloadUrl,
            ], 200, $headers);
        }

        return $storage->response($path, $name, $headers, 'inline');
    }
}
