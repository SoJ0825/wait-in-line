<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $apiToken;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory('App\User')->create();

        $this->apiToken = $this->user->api_token;
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

    public function testUnAuthUserCantGetACard()
    {
        $this->json('POST', '/cards/' . $this->user->id)->assertStatus(401);
    }

    public function testAuthUserCanGetACard()
    {
        $response = $this->json(
            'POST',
            '/cards/' . $this->user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['card'], 1);
    }

    public function testUserCantResetCard()
    {
        $response = $this->json(
            'DELETE',
            '/cards',
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        )->assertStatus(401)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
    }

    public function testAdminCanResetCard()
    {
        $adminUser = factory('App\User')->create(['name' => 'ttn']);

        $response = $this->json(
            'DELETE',
            '/cards',
            [],
            ['Authorization' => 'Bearer ' . $adminUser->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
    }

    public function testUserCantSkipCard()
    {
        $response = $this->json(
            'PATCH',
            '/cards',
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        )->assertStatus(401)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'fail');
    }

    public function testAdminCanSkipCard()
    {
        $adminUser = factory('App\User')->create(['name' => 'ttn']);

        $response = $this->json(
            'PATCH',
            '/cards',
            [],
            ['Authorization' => 'Bearer ' . $adminUser->api_token]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['current'], 2);
    }

    public function testUserCanShowCard()
    {
        $response = $this->json(
            'GET',
            '/cards/' . $this->user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertNull($response['card']);

        $this->json(
            'POST',
            '/cards/' . $this->user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        );

        $response = $this->json(
            'GET',
            '/cards/' . $this->user->id,
            [],
            ['Authorization' => 'Bearer ' . $this->apiToken]
        )->assertStatus(200)
        ->decodeResponseJson();

        $this->assertEquals($response['result'], 'success');
        $this->assertEquals($response['card'], 1);
    }
}
