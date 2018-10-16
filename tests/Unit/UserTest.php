<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanGenerateApiToken()
    {
        $user = factory('App\User')->create();

        $originalApiToken = $user->api_token;

        $user->generateApiToken();

        $apiToken = $user->api_token;

        $this->assertFalse($originalApiToken === $apiToken);
    }
}
