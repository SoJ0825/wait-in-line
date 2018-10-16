<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    protected $apiToken;

    public function setUp()
    {
        parent::setUp();

        $user = factory('App\User')->create();

        $this->apiToken = $user->api_token;
    }

    public function testItCantGetCurrentCardWithoutApiToken()
    {
        $this->json('GET', '/cards')->assertStatus(401);
    }

    public function testItCanGetCurrentCardWithApiToken()
    {
        $response = $this->json(
            'GET',
            '/cards',
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['current'], 1);
    }
}
