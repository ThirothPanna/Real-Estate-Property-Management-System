<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\LeaseDocument;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LeaseDocumentController extends Controller
{
    /**
     * Upload a new lease document.
     */
    public function store(Request $request)
    {
        $request->validate([
            'document' => [
                'required',
                'file',
                'max:10240', // 10 MB
                'mimes:pdf,jpg,jpeg,png,doc,docx',
            ],
        ]);

        $file = $request->file('document');

        $originalName = $file->getClientOriginalName();
        $mimeType     = $file->getClientMimeType();
        $size         = $file->getSize();

        // Store in storage/app/public/leases/{user_id}/...
        $path = $file->store('leases/' . Auth::id(), 'public');

        $doc = LeaseDocument::create([
            'user_id'       => Auth::id(),
            'original_name' => $originalName,
            'file_path'     => $path,
            'mime_type'     => $mimeType,
            'size'          => $size,
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Document uploaded',
            'body'    => $originalName . ' was uploaded successfully.',
            'icon'    => 'lease',
        ]);

        return back()->with('status', 'Document uploaded successfully.');
    }

    /**
     * Download a lease document.
     */
    public function download(LeaseDocument $document)
    {
        $this->checkOwnership($document);

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found on disk.');
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_name
        );
    }

    /**
     * Delete a lease document.
     */
    public function destroy(LeaseDocument $document)
    {
        $this->checkOwnership($document);

        // Remove the file from disk
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return back()->with('status', 'Document deleted.');
    }

    private function checkOwnership(LeaseDocument $document): void
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }
    }
}