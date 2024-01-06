<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Album;

class AlbumCompilationWebhookController
{
    private const ALBUM_STATUS_IN_PROGRESS = 'in_progress';
    private const ALBUM_STATUS_COMPLETED = 'completed';
    private const ALBUM_STATUS_FAILED = 'failed';
    private Album $album; 

    public function compileAlbum(Request $request)
    {
        // Check if the album is already in progress
        if ($this->getCurrentAlbumStatus($request->input('id')) === self::ALBUM_STATUS_IN_PROGRESS) {
            return response()->json(['error' => 'Album compilation is already in progress.'], 400);
        }
        // Update the album status to in progress
        $this->updateAlbumStatus(self::ALBUM_STATUS_IN_PROGRESS);

        try {
            // Call the third-party API to process and optimize images
            $response = Http::get('https://yesno.wtf/api', [
                'images' => $request->input('images'),
                // Other parameters as needed
            ]);
            
            // Check if the API call was successful
            if ($response->successful()) {
                // Update the album status to completed
                $this->updateAlbumStatus(self::ALBUM_STATUS_COMPLETED);

                // Perform any additional actions on successful compilation
                // For example: Notify user, save results, etc.

                return response()->json(['message' => 'Album compilation completed successfully.']);
            } else {
                // Log the error response from the third-party API
                Log::error('Third-party API failed: ' . $response->body());

                // Update the album status to failed
                $this->updateAlbumStatus(self::ALBUM_STATUS_FAILED);

                return response()->json(['error' => 'Album compilation failed. Please try again.'], 500);
            }
        } catch (\Exception $e) {
            // Log any exceptions that occur during the compilation process
            Log::error('Album compilation exception: ' . $e->getMessage());

            // Update the album status to failed
            $this->updateAlbumStatus(self::ALBUM_STATUS_FAILED);

            return response()->json(['error' => 'Album compilation failed. Please try again.'], 500);
        }
    }

    private function getCurrentAlbumStatus($albumId)
    {
        // Logic to retrieve the current album status from your storage (e.g., database)
        // Return self::ALBUM_STATUS_IN_PROGRESS, self::ALBUM_STATUS_COMPLETED, or self::ALBUM_STATUS_FAILED
        $this->album = Album::findOrFail($albumId);
        return $this->album->status;

    }

    private function updateAlbumStatus($status)
    {
        // Logic to update the album status in your storage (e.g., database)
        $this->album->update(['status'=> $status]);
    }
}