<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AlbumRequest;
use App\Models\Album;
use App\Models\Photo;
use Inertia\Inertia;

class AlbumController extends Controller
{
    /**
     * Display a paginated list of albums with associated photos for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $albums = Album::with('photos')->where(['user_id' => $user->id])->paginate();
        
        return response()->json($albums);
    }

    /**
     * Store a newly created album for the authenticated user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AlbumRequest $request)
    {
            
        $user = $request->user(); 
        $album = $user->albums()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'layout' => $request->input('layout'),
        ]);

        return response()->json($album, 201);
    }

    /**
     * Display the specified album with associated photos.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $album = Album::with('photos')->findOrFail($id);
        return response()->json($album);
    }

    /**
     * Update the specified album details.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AlbumRequest $request, $id)
    {
        $album = Album::findOrFail($id);
        $album->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'layout' => $request->input('layout'),
        ]);

        return response()->json($album);
    }

    /**
     * Add a photo to a specific album.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPhotoToAlbum(Request $request)
    {
        $album = Album::findOrFail($request->input('albumId'));
        $photo = Photo::findOrFail($request->input('photoId'));  
        if (!$album->photos->contains($request->input('photoId'))) {  
            return response()->json(
                $album->photos()->attach($photo)
            );   
        } 
        return response()->json(['error' => 'Photo is already in the album.'], 400);
    }

    /**
     * Remove the specified album.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $album = Album::findOrFail($id);
        $album->delete();
        return response()->json(['message' => 'Album deleted successfully']);
    }

    /**
     * Retrieve paginated photos for a specific album.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlbumPhotos(Request $request, $id)
    {
        $user = $request->user();
        $perPage = $request->get('per_page', 5);

        $photosInAlbum = Photo::whereHas('albums', function ($query) use ($id) {
            $query->where('album_id', $id);
        })->paginate($perPage);

        return response()->json($photosInAlbum);
    }

    /**
     * Render the Inertia.js view for album dashboard.
     * Used in the album dashboard view
     *
     * @param Request $request
     * @param int|null $id
     * @return \Inertia\Response
     */
    public function albumDashboard(Request $request, $id = null)
    {
        $user = $request->user();
        $album = Album::findOrFail($id);
        return Inertia::render('AlbumDashboard', ['albumId' => $id, 'album' => $album]);
    }

    /**
     * Remove a photo from a specific album.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removePhotoFromAlbum($id, $id2)
    {
        $photo = Photo::findOrFail($id);   
        $album = Album::findOrFail($id2);    
        return response()->json(
            $album->photos()->detach($photo)
        );     
    }
}
