<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Models\Album;
use App\Mail\CompilationDoneEmail;

class AlbumCompilationWebhookController
{
    public const ALBUM_STATUS_IN_PROGRESS = 'in_progress';
    public const ALBUM_STATUS_COMPLETED = 'completed';
    public const ALBUM_STATUS_FAILED = 'failed';
    private Album $album; 

    /**
     * Compile the album by processing and optimizing images via a third-party API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

                // Email the user to notify that the album compilation process is done
                $userEmail = $request->user()->email; 

                Mail::to($userEmail)->send(new CompilationDoneEmail);

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

    /**
     * Get the current status of the specified album.
     *
     * @param int $albumId
     * @return string
     */
    private function getCurrentAlbumStatus($albumId)
    {
        // Logic to retrieve the current album status from database
        // Return self::ALBUM_STATUS_IN_PROGRESS, self::ALBUM_STATUS_COMPLETED, or self::ALBUM_STATUS_FAILED
        $this->album = Album::findOrFail($albumId);
        return $this->album->status;
    }

    /**
     * Update the status of the current album.
     *
     * @param string $status
     * @return void
     */
    private function updateAlbumStatus($status)
    {
        $this->album->update(['status' => $status]);
    }
}