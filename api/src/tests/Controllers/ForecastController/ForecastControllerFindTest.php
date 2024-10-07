<?php

namespace Tests\Controllers\ForecastController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Validator;

class ForecastControllerFindTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_forecast_without_city(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?country=US');

        $data = $response->json();

        $response->assertUnprocessable();

        $this->assertIsArray($data);
        $this->assertIsArray($data['errors']);
        $this->assertEquals($data['message'], "The city field is required.");
    }

    public function test_find_forecast_without_country(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York');

        $data = $response->json();

        $response->assertUnprocessable();

        $this->assertIsArray($data);
        $this->assertIsArray($data['errors']);
        $this->assertEquals($data['message'], "The country field is required.");
    }

    public function test_find_forecast_without_city_and_without_country(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast');

        $data = $response->json();

        $response->assertUnprocessable();

        $this->assertIsArray($data);
        $this->assertIsArray($data['errors']);
        $this->assertEquals($data['message'], "The city field is required. (and 1 more error)");
    }

    public function test_find_forecast_city_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=ABCDEFGH&country=US');

        $data = $response->json();

        $response->assertNotFound();

        $this->assertIsArray($data);
        $this->assertEquals($data['message'], "City not found.");
    }

    public function test_find_forecast_country_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=XY');

        $data = $response->json();

        $response->assertNotFound();

        $this->assertIsArray($data);
        $this->assertEquals($data['message'], "City not found.");
    }

    public function test_find_forecast_reached_max_number(): void
    {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response2 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=Colorado&country=US');

        $response3 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=San Diego&country=US');

        $response1->assertOk();
        $response2->assertOk();
        $response3->assertOk();

        $response4 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=San Diego&country=US');

        $response4->assertInternalServerError();

        $data = $response4->json();

        $this->assertIsArray($data);
        $this->assertEquals($data['message'], "Maximum number of forecast reached.");
    }

    public function test_find_forecast_same_city_massive(): void
    {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response2 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response3 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response4 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response5 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response1->assertOk();
        $response2->assertOk();
        $response3->assertOk();
        $response4->assertOk();
        $response5->assertOk();
    }

    public function test_find_forecast_check_types(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $data = $response->json();

        $validator = Validator::make($data, [
            'uuid' => 'uuid',
            'created_at' => 'date',
            'city' => 'string',
            'country' => 'string',
            'description' => 'string',
            'icon' => 'url',
            'temperature' => 'decimal:0,2',
            'minimumTemperature' => 'decimal:0,2',
            'maximumTemperature' => 'decimal:0,2',
            'humidity' => 'integer',
            'wind' => 'decimal:0,2'
        ]);

        $this->assertEquals(count($validator->validate()), count($data));

        $this->assertIsArray($data);
    }

    public function test_find_forecast_check_content(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $data = $response->json();

        $this->assertEquals($data['city'], "New York");
        $this->assertEquals($data['country'], "US");

        $this->assertIsArray($data);
    }
}
