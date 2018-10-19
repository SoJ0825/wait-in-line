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

        $this->assertEquals(1, $user->card);

        $desk->serveCustomer($user);

        $desk = $desk->fresh();
        $user = $user->fresh();

        $this->assertEquals($desk->user_id, $user->id);
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
}
