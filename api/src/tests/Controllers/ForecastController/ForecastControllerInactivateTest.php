<?php

namespace Tests\Controllers\ForecastController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;

class ForecastControllerInactivateTest extends TestCase
{
    use RefreshDatabase;

    public function test_forecast_inactivate(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=San Diego&country=US');
        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);
        $uuid = $data['uuid'];

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');
        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->delete("/api/forecast/{$uuid}");
        $response->assertNoContent();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');
        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function test_forecast_inactivated_already(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast?city=San Diego&country=US');
        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);
        $uuid = $data['uuid'];

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');
        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(1, $data);

        //official inactivation
        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->delete("/api/forecast/{$uuid}");
        $response->assertNoContent();

        //inactivating an already inactivated uuid
        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->delete("/api/forecast/{$uuid}");
        $response->assertNoContent();

        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get('/api/forecast/list');
        $response->assertOk();

        $data = $response->json();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function test_forecast_inactivate_unexistent(): void
    {
        $user = User::factory()->create();

        $uuid = "27bd9eed-1cfe-4316-9020-8b0e1cdc444a";

        //its already inactivated uuid
        $response = $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->delete("/api/forecast/{$uuid}");
        $response->assertNoContent();
    }
}
