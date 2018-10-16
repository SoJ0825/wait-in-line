<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testUsersCanRegister()
    {
        $response = $this->json('POST', '/register', [
            'name' => 'ttn',
            'email' => 'ttn@example.com',
            'password' => 'foobar',
        ])->assertStatus(200)->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');

        $this->assertDatabaseHas('users', [
            'name' => 'ttn',
            'email' => 'ttn@example.com',
        ]);
    }
}
