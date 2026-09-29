<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\StoredDocumentPreview;

class LeaseController extends Controller
{
    public function download(Lease $lease)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($lease->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$storage->exists($lease->file_path)) {
            abort(404);
        }

        return $storage->download(
            $lease->file_path,
            $lease->original_name
        );
    }

    public function view(Lease $lease, StoredDocumentPreview $preview)
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($lease->user_id !== Auth::id()) {
            abort(403);
        }

        if (! $storage->exists($lease->file_path)) {
            abort(404);
        }

        return $preview->response(
            $lease->file_path,
            $lease->original_name,
            $lease->mime_type,
            route('tenant.leases.download', $lease)
        );
    }

    public function acknowledge(Lease $lease)
    {
        if ($lease->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$lease->isAcknowledged()) {
            $lease->update(['acknowledged_at' => now()]);
        }

        return back()->with('status', 'Lease acknowledged successfully.');
    }
}