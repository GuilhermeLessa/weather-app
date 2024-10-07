<?php

namespace Tests\Controllers\ForecastController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Validator;

class ForecastControllerListTest extends TestCase
{
    use RefreshDatabase;

    public function test_count_list_forecast(): void
    {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response2 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=San Diego&country=US');

        $response3 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=Milwaukee&country=US');

        $response1->assertOk();
        $response2->assertOk();
        $response3->assertOk();

        $response4 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');

        $response4->assertOk();

        $data = $response4->json();
        $this->assertIsArray($data);
        $this->assertCount(3, $data);
    }

    public function test_count_list_forecast_multi_user(): void
    {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response2 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=San Diego&country=US');

        $response3 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=Milwaukee&country=US');

        $response1->assertOk();
        $response2->assertOk();
        $response3->assertOk();

        $response4 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');

        $response4->assertOk();

        $data = $response4->json();
        $this->assertIsArray($data);
        $this->assertCount(3, $data);
    }

    public function test_list_forecast_types(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response2 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');

        $data = $response2->json();

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

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=New York&country=US');

        $response2 = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');

        $data = $response2->json();

        $this->assertIsArray($data);
        $this->assertIsArray($data[0]);

        $this->assertEquals($data[0]['city'], "New York");
        $this->assertEquals($data[0]['country'], "US");

        $this->assertIsArray($data);
    }
}
