<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PhotoRequest;

class UploadPhotoController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $path = $request->file('photo')->store('photos');

        return $user->photos()->create(['path' => $path]);
    }
    
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 6);
        $user = $request->user();
        $images = $user->photos()->paginate($perPage);        
        return response()->json($images);
    }


    public function upload(PhotoRequest $request)
    {
           // Validate the request using the PhotoUploadRequest
        $validatedData = $request->validated();

        $images = [];
        $user = $request->user();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $image) {
                $imageName = time() . $key . '.' . $image->getClientOriginalExtension();
                $path = $image->move(public_path('uploads'), $imageName);
                $user->photos()->create(['path' => $imageName]);
                $images[] = $imageName;
            }
        }
        return response()->json(['images' => $images], 200);
    }
    
    public function destroy($id) {
        $image = Photo::findOrFail($id);
        Storage::disk('public')->delete('images/' . $image->name);

        // Delete the record from the database
        $image->delete();

        return response()->json(['message' => 'Image deleted successfully']);
    }

}
