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
        $desk = factory('App\Desk', 2)->create();

        $response = $this->json(
            'GET',
            '/desks',
            [],
            ['Authorization' => 'Bearer ' . $this->admin->api_token]
        )->assertStatus(200)
        ->decodeResponesJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertCount($response['desks']);

        $this->assertEquals($response['desk'][0]['id'], 1);
        $this->assertNull($response['desk'][0]['user_id']);

        $this->assertEquals($response['desk'][1]['id'], 2);
        $this->assertNull($response['desk'][1]['user_id']);
    }
}
