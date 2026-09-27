<?php
namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyPhoto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyPhotoController extends Controller
{
    public function destroy(PropertyPhoto $photo)
    {
        $this->checkOwnership($photo->property);

        if (Storage::disk('public')->exists($photo->file_path)) {
            Storage::disk('public')->delete($photo->file_path);
        }

        $photo->delete();

        return back()->with('status', 'Photo removed successfully.');
    }

    public function setCover(PropertyPhoto $photo)
    {
        $this->checkOwnership($photo->property);

        $photo->property()->update([
            'cover_photo_id' => $photo->id,
        ]);

        $photo->property->photos()->update(['is_cover' => false]);
        $photo->update(['is_cover' => true]);

        return back()->with('status', 'Cover photo updated.');
    }

    private function checkOwnership(Property $property): void
    {
        if ($property->landlord_id !== Auth::id()) {
            abort(403);
        }
    }
}