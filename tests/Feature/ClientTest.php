<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use WithFaker;

    public function clientData()
    {
        return [
            'name' => $this->faker->name,
            'address1' => $this->faker->address,
            'address2' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
            'zipCode' => $this->faker->postcode,
            'phoneNo1' => $this->faker->phoneNumber,
            'user' => [
                'firstName' => $this->faker->firstName,
                'lastName' => $this->faker->lastName,
                'email' => $this->faker->email,
                'password' => 'administrator',
                'passwordConfirmation' => 'administrator',
                'phone' => $this->faker->phoneNumber
            ]

        ];
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_server_is_live()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_client_api_validation()
    {
        $postData = $this->clientData();
        unset($postData['phoneNo1']);

        $response = $this->postJson('/api/register', $postData);
        // print_r($response->json());

        $response->assertStatus(200)
            ->assertJson(['error' => 1]);

        $response->assertJson(function (AssertableJson $json) {
            return $json
                /* check errorbag is not empty */
                ->whereType('errorBag', 'array')
                /* check errorbag contains the not validated key */
                ->has('errorBag.phoneNo1')
                ->etc();
        });
    }


    public function test_client_api_validatoin_for_users()
    {
        $postData = $this->clientData();
        /* tamper with request data  */
        $postData['user']['email'] = 'abc';
        $postData['user']['passwordConfirmation'] = 'admin';

        $response = $this->postJson('/api/register', $postData);
        // print_r($response->json());

        $response->assertStatus(200)
            ->assertJson(['error' => 1]);


        $response->assertJson(function (AssertableJson $json) {
            return $json
                /* check errorbag is not empty */
                ->whereType('errorBag', 'array')

                ->etc();
        });

        $response->assertJsonStructure([
            'errorBag' => [
                'user.email',
                'user.passwordConfirmation'
            ]
        ]);
    }

    public function test_check_client_is_registered()
    {
        $postData = $this->clientData();



        $response = $this->postJson('/api/register', $postData);

        // print_r($response->json());

        $response->assertStatus(200)->assertJson(['success' => 1]);

        $email = $postData['user']['email'];
        $user = \App\Models\User::where('email', $email)->first();

        /* to reduce external api calls we are doing most of the assertion in same test. */

        /* check if user was saved */
        $this->assertNotNull($user);

        /* check if client was saved as well and client_id correctly stored in users. */
        $this->assertNotNull($user->client);

        /* verify the user client has the same name as the post data */
        $this->assertEquals($postData['name'], $user->client->client_name);

        /* test client start validity is set as today */
        $this->assertEquals(date("Y-m-d"), $user->client->start_validity);

        /* test client end validity is set as 15 days fro mtoday */
        $this->assertEquals(date("Y-m-d", strtotime("+15 days")), $user->client->end_validity);

        /* test redis data */
        $cachedValue = Redis::get($user->client->latLngCacheKey());
        /* test something is cached */
        $this->assertNotNull($cachedValue);

        $cachedArray = @json_decode($cachedValue, true);
        /* test cached value has lat */
        $this->assertNotNull($cachedArray['lat']);

        /* test if cached value matches value in the clients table */
        $this->assertEquals($cachedArray['lat'], $user->client->latitude);
    }


    public function test_client_listing()
    {
        $response = $this->get("/api/accounts");

        $response->assertStatus(200);


        $response->assertJsonStructure([
            'data',
            'links' => [
                'path',
                'firstPageUrl',
                'lastPageUrl',
                'prevPageUrl',
                'nextPageUrl'
            ],
            'meta' => [
                'currentPage',
                'lastPage',
                'perPage',
                'to',
                'total',
                'count'
            ]
        ]);
    }
}
