<?php

namespace Tests\Controllers\ForecastController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\Resources\TestCaseWithApi;

class ForecastControllerFindTest extends TestCaseWithApi
{
    use RefreshDatabase;

    public function test_find_forecast_without_city(): void
    {
        $response = $this->find("", "US");
        $response->assertUnprocessable();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertIsArray($data['errors']);
        $this->assertEquals($data['message'], "The city field is required.");
    }

    public function test_find_forecast_without_country(): void
    {
        $response = $this->find("New York", "");
        $response->assertUnprocessable();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertIsArray($data['errors']);
        $this->assertEquals($data['message'], "The country field is required.");
    }

    public function test_find_forecast_without_city_and_without_country(): void
    {
        $response = $this->find("", "");
        $response->assertUnprocessable();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertIsArray($data['errors']);
        $this->assertEquals($data['message'], "The city field is required. (and 1 more error)");
    }

    public function test_find_forecast_city_not_found(): void
    {
        $response = $this->find("ABCDEFGH", "US");
        $response->assertNotFound();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertEquals($data['message'], "City not found.");
    }

    public function test_find_forecast_country_not_found(): void
    {
        $response = $this->find("New York", "XY");
        $response->assertNotFound();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertEquals($data['message'], "City not found.");
    }

    public function test_find_forecast_reached_max_number(): void
    {
        $user = User::factory()->create();

        $response1 = $this->find("New York", "US", $user);
        $response2 = $this->find("Colorado", "US", $user);
        $response3 = $this->find("San Diego", "US", $user);

        $response4 = $this->find("San Diego", "US", $user);
        $response4->assertInternalServerError();

        $data = $response4->json();
        $this->assertIsArray($data);
        $this->assertEquals($data['message'], "Maximum number of forecast reached.");
    }

    public function test_find_forecast_same_city_massive(): void
    {
        $user = User::factory()->create();

        $response1 = $this->find("New York", "US", $user);
        $response2 = $this->find("New York", "US", $user);
        $response3 = $this->find("New York", "US", $user);
        $response4 = $this->find("New York", "US", $user);
        $response5 = $this->find("New York", "US", $user);

        $response1->assertOk();
        $response2->assertOk();
        $response3->assertOk();
        $response4->assertOk();
        $response5->assertOk();
    }

    public function test_find_forecast_check_types(): void
    {
        $response = $this->find("New York", "US");

        $data = $response->json();
        $this->assertIsArray($data);

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
    }

    public function test_find_forecast_check_content(): void
    {
        $response = $this->find("New York", "US");

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertEquals($data['city'], "New York");
        $this->assertEquals($data['country'], "US");
    }

}
