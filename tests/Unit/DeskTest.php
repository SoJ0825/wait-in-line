<?php

namespace Tests\Unit;

use App\Desk;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeskTest extends TestCase
{
    use RefreshDatabase;

    public function testItCanServeACustomer()
    {
        $desk = factory('App\Desk')->create();
        $card = factory('App\Card')->create();
        $user = factory('App\User')->create();

        $card->sendTo($user);

        $user = $user->fresh();
        $userCard = $user->card;

        $this->assertEquals($userCard, $user->card);

        $desk->serveCustomer($user);

        $desk = $desk->fresh();
        $user = $user->fresh();

        $this->assertEquals($desk->user_id, $user->id);
        $this->assertEquals($desk->serving_card, $userCard);
        $this->assertNull($user->card);
    }

    public function testItCanCheckNoEmptyDesk()
    {
        $this->assertNull(Desk::findEmpty());
    }

    public function testItCanFindEmptyDesk()
    {
        $emptyDesk = factory('App\Desk')->create(['user_id' => null]);
        $serveDesk = factory('App\Desk')->create();

        $this->assertEquals($emptyDesk->id, Desk::findEmpty()->id);
    }

    public function testItCanCheckIsServing()
    {
        $serveDesk = factory('App\Desk')->create(['serving_card' => 1]);

        $this->assertTrue($serveDesk->isServing());
    }

    public function testItCanLeaveCustomer()
    {
        $user = factory('App\User')->create();
        $card = factory('App\Card')->create();
        $serveDesk = factory('App\Desk')->create();

        $card->sendTo($user);

        $serveDesk->serveCustomer($user);

        $this->assertTrue($serveDesk->isServing());
        $this->assertNotNull($serveDesk->serving_card);

        $serveDesk->leaveCustomer();

        $this->assertFalse($serveDesk->isServing());
        $this->assertNull($serveDesk->serving_card);
    }

    public function testItCanCheckIfOverReleasedCard()
    {
        $servingUser = factory('App\User')->create();
        $waitingUser = factory('App\User')->create();
        $card = factory('App\Card')->create();
        $desk = factory('App\Desk')->create();

        $card->sendTo($servingUser);
        $card->sendTo($waitingUser);

        $desk->serveCustomer($servingUser);

        $this->assertFalse(Desk::isOverReleasedCard());

        $desk->serveCustomer($waitingUser);

        $this->assertTrue(Desk::isOverReleasedCard());
    }

    public function testItCanSkipACard()
    {
        $user = factory('App\User')->create();
        $card = factory('App\Card')->create();
        $desks = factory('App\Desk', 5)->create(['user_id' => null]);

        $card->sendTo($user);
        $desks[0]->serveCustomer($user);

        Desk::skip();

        $this->assertEquals(2, Desk::servingCard());
    }
}
