<?php

namespace Tests\Feature;

use App\Desk;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeskTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp()
    {
        parent::setUp();

        $this->admin = factory('App\User')->create(['name' => 'ttn']);
    }

    public function testAdminCanListDesks()
    {
        $desk = factory('App\Desk', 2)->create(['user_id' => null]);

        $response = $this->json(
            'GET',
            '/desks',
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertCount(2, $response['desks']);

        $this->assertEquals($response['desks'][0]['id'], 1);
        $this->assertNull($response['desks'][0]['user_id']);
        $this->assertNull($response['desks'][0]['serving_card']);

        $this->assertEquals($response['desks'][1]['id'], 2);
        $this->assertNull($response['desks'][1]['user_id']);
        $this->assertNull($response['desks'][1]['serving_card']);
    }

    public function testDeskCantServeNoCardCustomer()
    {
        $desk = factory('App\Desk')->create(['user_id' => null]);
        $user = factory('App\User')->create();

        $response = $this->json(
            'POST',
            '/desks/' . $user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
        $this->assertEquals($response['message'], 'Draw a card first');
    }

    public function testDeskCantServeNotLineOfHeadCustomer()
    {
        $desk = factory('App\Desk')->create(['user_id' => null]);
        $card = factory('App\Card')->create();
        $headUser = factory('App\User')->create();
        $user = factory('App\User')->create();

        $card->sendTo($headUser);
        $card->sendTo($user);

        $response = $this->json(
            'POST',
            '/desks/' . $user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
        $this->assertEquals($response['message'], 'Not your turn');
    }

    public function testDeskCantServeWhenNoEmptyDesk()
    {
        $desk = factory('App\Desk')->create();
        $card = factory('App\Card')->create();
        $servingUser = factory('App\User')->create();
        $user = factory('App\User')->create();

        $card->sendTo($servingUser);
        $card->sendTo($user);

        $desk->serveCustomer($servingUser);

        $response = $this->json(
            'POST',
            '/desks/' . $user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
        $this->assertEquals($response['message'], 'No empty desk');
    }

    public function testAdminCanLeadCustomerToDesk()
    {
        $desk = factory('App\Desk')->create(['user_id' => null]);
        $card = factory('App\Card')->create();
        $user = factory('App\User')->create();

        $card->sendTo($user);

        $response = $this->json(
            'POST',
            '/desks/' . $user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['desk'], 1);
    }

    public function testAdminCanLeadOverCustomerToDesk()
    {
        $desks = factory('App\Desk', 5)->create(['user_id' => null]);
        $card = factory('App\Card')->create();
        $over = factory('App\User')->create();
        $serving = factory('App\User')->create();

        $card->sendTo($over);
        $card->sendTo($serving);

        $desks[0]->serveCustomer($serving);

        $response = $this->json(
            'POST',
            '/desks/' . $over->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['desk'], 2);
    }

    public function testItCheckDeskIsServing()
    {
        $desk = factory('App\Desk')->create(['user_id' => null]);

        $response = $this->json(
            'DELETE',
            '/desks/' . $desk->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
        $this->assertEquals($response['message'], 'Desk is not serving');
    }

    public function testItCanLeaveCustomer()
    {
        $desk = factory('App\Desk')->create(['serving_card' => 1]);

        $userLeaved = $desk->user_id;

        $response = $this->json(
            'DELETE',
            '/desks/' . $desk->id,
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['user'], $userLeaved);
    }

    public function testItCantSkipOverReleasedCard()
    {
        $user = factory('App\User')->create();
        $card = factory('App\Card')->create();
        $desk = factory('App\Desk')->create();

        $card->sendTo($user);
        $desk->serveCustomer($user);

        $response = $this->json(
            'PATCH',
            '/desks',
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
        $this->assertEquals($response['message'], 'Can\'t skip over released card');
    }

    public function testItCantSkip()
    {
        $users = factory('App\User', 2)->create();
        $card = factory('App\Card')->create();
        $desks = factory('App\Desk', 5)->create(['user_id' => null]);

        $card->sendTo($users[0]);
        $desks[0]->serveCustomer($users[0]);
        $card->sendTo($users[1]);

        $response = $this->json(
            'PATCH',
            '/desks',
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['serving'], 2);
    }
}
