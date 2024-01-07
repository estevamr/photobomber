<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Photo;
use App\Http\Controllers\UploadPhotoController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_images_for_authenticated_user()
    {
        $user = User::factory()->create();
        $photos = Photo::factory(10)->create(['user_id' => $user->id]);

        // Simulate a GET request to the index endpoint
        $response = $this->actingAs($user)->get('/images'); // Replace '/your-route' with the actual route

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that the response content is in JSON format
        $response->assertJsonStructure(['data', 'links', 'current_page', 'per_page', 'to', 'total']);
        
        // Assert that the response JSON contains the expected data
        $response->assertJsonCount(6, 'data'); // Assuming per_page is set to 6 in the controller
        
    }

    /** @test */
    public function it_uploads_a_photo()
    {
        
        $user = User::factory()->create();
        // faking images
        $img1 = UploadedFile::fake()->image('image1.jpg');
        $img2 = UploadedFile::fake()->image('image2.jpg');
        
        // Simulate a POST request to the upload endpoint
        $response = $this->actingAs($user)->post('/upload', [
            'images' => [
                $img1,
                $img2,
            ],
        ]);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // assert that the images are created and stored in the expected location
        foreach ($response->json('images') as $imageName) {
            $this->assertFileExists(public_path('uploads/' . $imageName));
        }
        
        $this->assertDatabaseHas('photos', [
            'user_id' => $user->id,
            'path' => $imageName,
        ]);
    }
    
    /** @test */
    public function it_deletes_a_photo()
    {
        // Assuming you have a user and a photo for testing
        $user = User::factory()->create();
        $photo = Photo::factory()->create(['user_id' => $user->id]);

        // Simulate a DELETE request to the destroy endpoint
        $response = $this->actingAs($user)->delete('/images/' . $photo->id);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that the response content is in JSON format
        // $response->assertJson();

        // Optionally, assert that the response JSON contains the expected data
        $response->assertJson(['message' => 'Image deleted successfully']);

        // Assert that the image is deleted from storage (replace 'public' with your disk name)
        $this->assertFileDoesNotExist(public_path('uploads/' . $photo->path));

        // Assert that the image is deleted from the database
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
    }
    
}
