<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanLogin()
    {
        $this->json('POST', '/register', [
            'name' => 'ttn',
            'email' => 'ttn@example.com',
            'password' => 'foobar',
        ]);

        $response = $this->json('POST', '/login', [
            'email' => 'ttn@example.com',
            'password' => 'foobar',
        ])->assertStatus(200)
          ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertArrayHasKey('api_token', $response);
    }
}
