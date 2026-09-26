
<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\PropertyPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyPhotoController extends Controller
{
    public function destroy(PropertyPhoto $photo)
    {
        $this->checkOwnership($photo);

        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $wasCover = $photo->is_cover;
        $property = $photo->property;

        $photo->delete();

        if ($wasCover) {
            $next = $property->photos()->first();
            if ($next) {
                $next->update(['is_cover' => true]);
            }
        }

        return back()->with('status', 'Photo removed.');
    }

    public function setCover(PropertyPhoto $photo)
    {
        $this->checkOwnership($photo);

        $property = $photo->property;
        $property->photos()->update(['is_cover' => false]);
        $photo->update(['is_cover' => true]);

        return back()->with('status', 'Cover photo updated.');
    }

    private function checkOwnership(PropertyPhoto $photo): void
    {
        if ($photo->property->landlord_id !== Auth::id()) {
            abort(403);
        }
    }
}