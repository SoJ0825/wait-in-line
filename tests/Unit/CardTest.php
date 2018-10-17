<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanSendToUser()
    {
        $card = factory('App\Card')->create();
        $user = factory('App\User')->create();

        $this->assertEquals(1, $card->current);
        $this->assertNull($user->card);

        $card->sendTo($user);

        $this->assertEquals(2, $card->fresh()->current);
        $this->assertEquals(1, $user->fresh()->card);
    }

    public function testItCanResetCard()
    {
        $card = factory('App\Card')->create();
        $userWithOneCard = factory('App\User')->create();
        $userWithTwoCard = factory('App\User')->create();

        $card->sendTo($userWithOneCard);
        $card->sendTo($userWithTwoCard);
        $card->sendTo($userWithTwoCard);

        $this->assertEquals(4, $card->fresh()->current);
        $this->assertEquals(1, $userWithOneCard->fresh()->card);
        $this->assertEquals(3, $userWithTwoCard->fresh()->card);

        $card->resetCurrent();

        $this->assertEquals(1, $card->fresh()->current);
        $this->assertNull($userWithOneCard->fresh()->card);
        $this->assertNull($userWithTwoCard->fresh()->card);
    }

    public function testItCanSkipCard()
    {
        $card = factory('App\Card')->create();

        $this->assertEquals(1, $card->current);

        $card->skip();

        $this->assertEquals(2, $card->fresh()->current);

        $card->skip(3);
        $this->assertEquals(5, $card->fresh()->current);
    }
}
