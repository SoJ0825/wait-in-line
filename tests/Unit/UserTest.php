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
        $desk = factory('App\Desk')->create(['user_id' => null]);

        $firstUser = factory('App\User')->create();
        $secondUser = factory('App\User')->create();
        $thirdUser = factory('App\User')->create();
        $notDrawUser = factory('App\User')->create();

        $card->sendTo($firstUser);
        $card->sendTo($secondUser);
        $card->sendTo($thirdUser);

        $desk->serveCustomer($secondUser);

        $this->assertFalse($firstUser->fresh()->isHeadOfLine());
        $this->assertFalse($secondUser->fresh()->isHeadOfLine());
        $this->assertTrue($thirdUser->fresh()->isHeadOfLine());
        $this->assertFalse($notDrawUser->fresh()->isHeadOfLine());
    }

    public function testItCanCheckIfIsOver()
    {
        $card = factory('App\Card')->create();
        $desk = factory('App\Desk')->create(['user_id' => null]);

        $overUser = factory('App\User')->create();
        $servingUser = factory('App\User')->create();

        $card->sendTo($overUser);
        $card->sendTo($servingUser);

        $desk->serveCustomer($servingUser);

        $this->assertTrue($overUser->fresh()->isOver());
    }

    public function testItCanCheckBeingServed()
    {
        $user = factory('App\User')->create();
        $desk = factory('App\Desk')->create(['user_id' => $user->id]);

        $this->assertTrue($user->isBeingServed());

        $desk->leaveCustomer($user);

        $this->assertFalse($user->isBeingServed());
    }
}
