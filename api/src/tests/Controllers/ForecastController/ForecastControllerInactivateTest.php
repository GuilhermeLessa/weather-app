<?php

namespace Tests\Controllers\ForecastController;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Resources\TestCaseWithApi;

class ForecastControllerInactivateTest extends TestCaseWithApi
{
    use RefreshDatabase;

    public function test_forecast_inactivate(): void
    {
        $user = User::factory()->create();

        $response = $this->find("San Diego", "US", $user);
        $data = $response->json();
        $uuid = $data['uuid'];

        $response = $this->inactivate($uuid, $user);
        $response->assertNoContent();

        $response = $this->list($user);
        $data = $response->json();
        $this->assertCount(0, $data);
    }

    public function test_forecast_inactivated_already(): void
    {
        $user = User::factory()->create();

        $response = $this->find("San Diego", "US", $user);
        $data = $response->json();
        $uuid = $data['uuid'];

        //official inactivation
        $response = $this->inactivate($uuid, $user);
        $response->assertNoContent();

        //inactivating an already inactivated uuid
        $response = $this->inactivate($uuid, $user);
        $response->assertNoContent();

        $response = $this->list($user);
        $data = $response->json();
        $this->assertCount(0, $data);
    }

    public function test_forecast_inactivate_unexistent(): void
    {
        $user = User::factory()->create();

        //its already inactivated uuid
        $uuid = "27bd9eed-1cfe-4316-9020-8b0e1cdc444a";
        $response = $this->inactivate($uuid, $user);
        $response->assertNoContent();

        $response = $this->list($user);
        $data = $response->json();
        $this->assertCount(0, $data);
    }

}
