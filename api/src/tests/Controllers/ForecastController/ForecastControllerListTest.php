<?php

namespace Tests\Controllers\ForecastController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\Resources\TestCaseWithApi;

class ForecastControllerListTest extends TestCaseWithApi
{
    use RefreshDatabase;

    public function test_count_list_forecast(): void
    {
        $user = User::factory()->create();

        $response = $this->find("New York", "US", $user);
        $response = $this->find("San Diego", "US", $user);
        $response = $this->find("Milwaukee", "US", $user);

        $response = $this->list($user);
        $data = $response->json();
        $this->assertCount(3, $data);
    }

    public function test_list_forecast_types(): void
    {
        $user = User::factory()->create();

        $response = $this->find("New York", "US", $user);

        $response = $this->list($user);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertIsArray($data[0]);

        $validator = Validator::make($data[0], [
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

        $this->assertEquals(count($validator->validate()), count($data[0]));
    }

    public function test_list_forecast_content(): void
    {
        $user = User::factory()->create();

        $response = $this->find("New York", "US", $user);

        $response = $this->list($user);
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertIsArray($data[0]);

        $this->assertEquals($data[0]['city'], "New York");
        $this->assertEquals($data[0]['country'], "US");
    }

}
