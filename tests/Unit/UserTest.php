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

    public function testItCanCheckIfUserDrawCard()
    {
        $card = factory('App\Card')->create();
        $user = factory('App\User')->create();

        $this->assertFalse($user->isInLine());

        $card->sendTo($user);

        tap($user->fresh(), function ($user) {
            $this->assertEquals(1, $user->card);
            $this->assertTrue($user->isInLine());
        });
    }

    public function testItCanCheckIfHeadOfLine()
    {
        $card = factory('App\Card')->create();

        $firstUser = factory('App\User')->create();
        $secondUser = factory('App\User')->create();
        $notDrawUser = factory('App\User')->create();

        $card->sendTo($firstUser);
        $card->sendTo($secondUser);

        $this->assertTrue($firstUser->fresh()->isHeadOfLine());
        $this->assertFalse($secondUser->fresh()->isHeadOfLine());
        $this->assertFalse($notDrawUser->fresh()->isHeadOfLine());
    }
}
