<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_home_preview(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(
            route('home.preview')
        );
    }


    public function test_home_preview_returns_successful_response(): void
    {
        $response = $this->get(
            route('home.preview')
        );

        $response->assertStatus(200);
    }
}   