<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        // もし認証が必要なら、ユーザーを作成してログインする
        // $user = \App\Models\User::factory()->create();
        // $response = $this->actingAs($user)->get('/');

        $response->assertStatus(302);
    }
}
