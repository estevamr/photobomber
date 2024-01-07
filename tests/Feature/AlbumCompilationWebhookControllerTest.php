<?php

namespace Tests\Feature;

use App\Http\Controllers\AlbumCompilationWebhookController;
use App\Mail\CompilationDoneEmail;
use App\Models\Album;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;


class AlbumCompilationWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Additional setup code if needed
    }

    /**
     * Test the compilation of an album if the album compilation is already in progress.
     */
    /** @test */
    public function it_doesnt_compile_album_already_in_progress()
    {
        // Mock the HTTP response from the third-party API
        Http::fake(['https://yesno.wtf/api' => Http::response(['answer' => 'yes'], 200)]);

        // Create a test user and album
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id, 
        'status' => AlbumCompilationWebhookController::ALBUM_STATUS_IN_PROGRESS]);

        // Call the controller method
        $response = $this->actingAs($user)->putJson('/compileAlbum',[ 'id'=> $album->id]);

        // Assert the response
        $response->assertJson([ 'error' => 'Album compilation is already in progress.']);
        $response->assertStatus(400);

    }


    /**
     * Test the compilation of an album with a successful response from the third-party API.
     */
    /** @test */
    public function it_compiles_album()
    {
        // Mock the HTTP response from the third-party API
        Http::fake(['https://yesno.wtf/api' => Http::response(['answer' => 'yes'], 200)]);
        Mail::fake();
        // Create a test user and album
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id, 
        'status' => null]);

        // Call the controller method
        $response = $this->actingAs($user)->putJson('/compileAlbum',[ 'id'=> $album->id]);

        // Assert the response
        $response->assertJson(['message' => 'Album compilation completed successfully.']);
        $response->assertStatus(200);

        // Assert that the album status has been updated to completed
        $this->assertEquals(AlbumCompilationWebhookController::ALBUM_STATUS_COMPLETED, $album->fresh()->status);

        
        // Assert that the user received the CompilationDoneEmail
        Mail::assertSent(CompilationDoneEmail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    /**
     * Test the compilation of an album with a failed response from the third-party API.
     */
    /** @test */
    public function it_tests_if_album_compilation_fails()
    {
        // Mock the HTTP response from the third-party API to simulate a failure
        Http::fake(['https://yesno.wtf/api' => Http::response(['error' => 'API error'], 500)]);

        // Create a test user and album
        $user = User::factory()->create();
        $album = Album::factory()->create(['user_id' => $user->id, 'status' => null]);

        // Call the controller method
        $response = $this->actingAs($user)->putJson('/compileAlbum', [
            'id' => $album->id,
        ]);

        // Assert the response
        $response->assertJson(['error' => 'Album compilation failed. Please try again.']);
        $response->assertStatus(500);

        // Assert that the album status has been updated to failed
        $this->assertEquals(AlbumCompilationWebhookController::ALBUM_STATUS_FAILED, $album->fresh()->status);

       
    }
}
