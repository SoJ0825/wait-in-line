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

    public function testItCanCheckIfUserIsAnAdmin()
    {
        $user = factory('App\User')->create();

        $this->assertFalse($user->isAdmin());

        $user = factory('App\User')->create(['name' => 'ttn']);

        $this->assertTrue($user->isAdmin());
    }
}
