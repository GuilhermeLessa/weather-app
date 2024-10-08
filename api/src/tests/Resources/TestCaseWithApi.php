<?php

namespace Tests\Resources;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;

abstract class TestCaseWithApi extends TestCase
{

    public function find(
        string $city,
        string $country,
        $user = null
    ) {
        if (!$user) {
            $user = User::factory()->create();
        }

        $query = "";
        if ($city) {
            $query = "?city={$city}";
        }
        if ($country) {
            $query = "?country={$country}";
        }
        if ($city && $country) {
            $query = "?city={$city}&country={$country}";
        }

        return $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get("/api/forecast{$query}");
    }

    public function list(
        $user = null
    ) {
        if (!$user) {
            $user = User::factory()->create();
        }

        return $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->get("/api/forecast/list");
    }

    public function inactivate(
        string $uuid,
        $user = null
    ) {
        if (!$user) {
            $user = User::factory()->create();
        }

        return $this->actingAs($user)
            ->withHeader("Accept", "application/json")
            ->delete("/api/forecast/{$uuid}");
    }
    
}
