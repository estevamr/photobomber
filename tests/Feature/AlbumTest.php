<?php

namespace Tests\Unit\Controllers;

use App\Models\Album;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Inertia\Testing\Assert;


class AlbumControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_albums_for_authenticated_user()
    {
        $user = User::factory()->create();
        $albums = Album::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('albumsList');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        // Assert that the response content is in JSON format
        $response->assertJsonStructure(['data', 'links', 'current_page', 'per_page', 'to', 'total']);
        // Add more assertions based on your specific response format
    }
    
    /** @test */
    public function it_creates_album_for_authenticated_user()
    {
        $user = User::factory()->create();
        $albumData = Album::factory()->raw();

        $response = $this->actingAs($user)->postJson('/album', $albumData);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('albums', $albumData + ['user_id' => $user->id]);
    }
    
    /** @test */
    public function it_show_albums_with_photos()
    {
        $user = User::factory()->create();
        // $album = Album::factory()->create(['user_id' => $user->id]);
        $photo = Photo::factory()->create(['user_id' => $user->id]);
        $album = Album::factory()->hasAttached($photo)->create(['user_id' => $user->id]);        

        $response = $this->actingAs($user)->getJson('/albumShow/'.$album->id);

        $response->assertOk();
        $response->assertJsonStructure(
            ['id', 'title', 'description', 'layout', 'status', 'user_id', 'photos']
        );
       
        // $response->assertJsonFragment(['photos' => $photo]);
    }
    
    /** @test */
    public function it_updates_album_details()
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id]);
        $newData = ['title' => 'Updated Title', 'layout' => 1];

        $response = $this->actingAs($user)->putJson('/album/'.$album->id, $newData);

        $response->assertOk();
        $this->assertDatabaseHas('albums', ['id' => $album->id, 'title' => 'Updated Title']);
    }
    
    /** @test */
    public function it_attaches_photos_to_an_album()
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id]);
        $photo = Photo::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/addPhotoToAlbum', [
            'albumId' => $album->id,
            'photoId' => $photo->id,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('album_photo', ['album_id' => $album->id, 'photo_id' => $photo->id]);
    }
    
    /** @test */
    public function it_deletes_an_album()
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson('/album/'. $album->id);

        $response->assertOk();
        $this->assertDatabaseMissing('albums', ['id' => $album->id]);
    }

    /** @test */
    public function it_gets_photos_in_an_album_returning_paginated_resultset()
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id]);
        $photos = Photo::factory()->count(5)->create(['user_id' => $user->id]);
        $album->photos()->attach($photos);

        $response = $this->actingAs($user)->getJson('/albumPhotos/'. $album->id);

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        // Add more assertions based on your specific response format
    }
    

    
    /** @test */
    public function it_detaches_a_photo_from_album()
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id]);
        $photo = Photo::factory()->create(['user_id' => $user->id]);
        $album->photos()->attach($photo);

        $response = $this->actingAs($user)->deleteJson('/album/remove/'.$photo->id.'/'.$album->id);

        $response->assertOk();
        $this->assertDatabaseMissing('album_photo', ['album_id' => $album->id, 'photo_id' => $photo->id]);
    }

    /** @test */
    public function it_returns_only_photos_that_are_not_attached_to_a_specific_album()
    {
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id]);
        $existingPhotos = Photo::factory()->count(5)->create(['user_id' => $user->id]);
        $album->photos()->attach($existingPhotos);
        // adding more photos without attaching to the album
        $missingPhoto = Photo::factory()->count(3)->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->getJson('/photosNotInAlbum/'.$album->id);

        $response->assertOk();
        // as we added 3 extra, it should return 3
        $response->assertJsonCount(3, 'data');
        // Assert that the response content is in JSON format
        $response->assertJsonStructure(['data', 'links', 'current_page', 'per_page', 'to', 'total']);
        
    }
   
}
