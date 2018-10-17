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
}
