<?php

namespace Tests\Feature;

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

        $this->assertEquals($response['desks'][1]['id'], 2);
        $this->assertNull($response['desks'][1]['user_id']);
    }
}
