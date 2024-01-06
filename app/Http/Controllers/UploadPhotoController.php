<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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
        $user = $request->user();

        $images = $user->photos()->get();
        // echo "test";
        
        return response()->json($images);
    }


    public function upload(Request $request)
    {
        $request->validate([
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

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
