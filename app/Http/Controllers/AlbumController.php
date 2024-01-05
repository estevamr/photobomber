<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Photo;

class AlbumController extends Controller
{
    
    // public function __invoke(Request $request)
    // {
    //     /** @var User $user */
    //     $user = $request->user();
        
    //     return $user->albums()->create([
    //         'title' => $request->input('title'),
    //         'description' => $request->input('description'),
    //         'layout' => $request->input('layout'),
    //     ]);
        
    // }
    
    public function index(Request $request)
    {
        //$albums = Album::latest()->paginate(10);
        $user = $request->user();
        
        $albums = Album::with('photos')->where(['user_id'=>$user->id])->paginate();
        
        return response()->json($albums);

    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        $user = $request->user();
        
        $album = $user->albums()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'layout' => $request->input('layout'),
        ]);
        

        return response()->json($album, 201);
    }

    public function show($id)
    {
        $album = Album::with('photos')->findOrFail($id);
        return response()->json($album);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $album = Album::findOrFail($request->input('id'));
        $album->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'layout' => $request->input('layout'),
        ]);

        return response()->json($album);
    }

    public function addPhotoToAlbum(Request $request) {
        $album = Album::findOrFail($request->input('albumId'));
        $photo = Photo::findOrFail($request->input('photoId'));
        if(1==1){
            return response()->json(
                $album->photos()->save($photo)
                
            );
        }
        
    }

    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();
        return response()->json(['message' => 'Album deleted successfully']);
    }
}

