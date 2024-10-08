<?php

namespace Tests\Repositories;

use App\Api\WeatherApi\OpenWeatherApi\OpenWeatherResponse;
use App\Models\ForecastModel;
use App\Repositories\ForecastRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tests\Resources\FakeWeatherResponseData;

class ForecastRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        $testerUser = User::factory()->create();
        $repository = new ForecastRepository($testerUser->id);
        $forecastModel = $repository->save($weatherResponse);

        $this->assertInstanceOf(ForecastModel::class, $forecastModel);
    }

    public function test_saved_forecast_types(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        $testerUser = User::factory()->create();
        $repository = new ForecastRepository($testerUser->id);
        $forecastModel = $repository->save($weatherResponse);

        $data = [
            'id' => $forecastModel->id,
            'uuid' => $forecastModel->uuid,
            'user_id' => $forecastModel->user_id,
            'city' => $forecastModel->city,
            'country' => $forecastModel->country,
            'weather_data' => $forecastModel->weather_data,
            'created_at' => $forecastModel->created_at,
            'updated_at' => $forecastModel->updated_at,
        ];

        $validator = Validator::make($data, [
            'id' => 'integer',
            'uuid' => 'uuid',
            'user_id' => 'integer',
            'city' => 'string',
            'country' => 'string',
            'weather_data' => 'json',
            'created_at' => 'date',
            'updated_at' => 'date'
        ]);

        $this->assertEquals(count($validator->validate()), count($data));
    }

    public function test_saved_forecast_content(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        $testerUser = User::factory()->create();
        $repository = new ForecastRepository($testerUser->id);
        $forecastModel = $repository->save($weatherResponse);

        $this->assertEquals($forecastModel->user_id, $testerUser->id);
        $this->assertEquals($forecastModel->city, $weatherResponse->getCity());
        $this->assertEquals($forecastModel->country, $weatherResponse->getCountry());
        $this->assertEquals($forecastModel->active, true);
    }

    public function test_count_activies_of_right_user(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        //creating 3 actives
        $testerUser1 = User::factory()->create();
        $repository1 = new ForecastRepository($testerUser1->id);
        $repository1->save($weatherResponse);
        $repository1->save($weatherResponse);
        $repository1->save($weatherResponse);

        //creating 2 inactives, should not count inactives
        $forecastModel4 = $repository1->save($weatherResponse);
        $forecastModel4->active = false;
        $forecastModel4->save();
        $forecastModel5 = $repository1->save($weatherResponse);
        $forecastModel5->active = false;
        $forecastModel5->save();

        //creating to other user, counter should not be affected through different users
        $testerUser2 = User::factory()->create();
        $repository2 = new ForecastRepository($testerUser2->id);
        $repository2->save($weatherResponse);
        $repository2->save($weatherResponse);

        $this->assertEquals($repository1->countActives(), 3);
        $this->assertEquals($repository2->countActives(), 2);
    }

    public function test_inactivate_all_of_right_user(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        //creating 2 actives
        $testerUser1 = User::factory()->create();
        $repository1 = new ForecastRepository($testerUser1->id);
        $repository1->save($weatherResponse);
        $repository1->save($weatherResponse);

        //creating to other user, should inactivate only to first user
        $testerUser2 = User::factory()->create();
        $repository2 = new ForecastRepository($testerUser2->id);
        $repository2->save($weatherResponse);
        $repository2->save($weatherResponse);

        $this->assertEquals($repository1->countActives(), 2);
        $this->assertEquals($repository2->countActives(), 2);

        $repository1->inactivateAll($weatherResponse->getCity(), $weatherResponse->getCountry());

        $this->assertEquals($repository1->countActives(), 0);
        $this->assertEquals($repository2->countActives(), 2);
    }

    public function test_inactivate_all_of_right_city(): void
    {
        $testerUser = User::factory()->create();
        $repository = new ForecastRepository($testerUser->id);

        //creating 1 record to city 1
        $weatherResponseData1 = FakeWeatherResponseData::get("New York", "US");
        $weatherResponse1 = new OpenWeatherResponse($weatherResponseData1);
        $repository->save($weatherResponse1);

        //creating 2 records to city 2
        $weatherResponseData2 = FakeWeatherResponseData::get("Colorado", "US");
        $weatherResponse2 = new OpenWeatherResponse($weatherResponseData2);
        $repository->save($weatherResponse2);
        $repository->save($weatherResponse2);

        $this->assertEquals($repository->countActives(), 3);
        $repository->inactivateAll("New York", "US");
        $this->assertEquals($repository->countActives(), 2);
    }

    public function test_find_first(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        $testerUser = User::factory()->create();
        $repository = new ForecastRepository($testerUser->id);

        $forecastModel = $repository->save($weatherResponse);
        $this->assertInstanceOf(ForecastModel::class, $forecastModel);

        $first = $repository->findFirst($forecastModel->uuid);
        $this->assertInstanceOf(ForecastModel::class, $first);
        $this->assertEquals($first->uuid, $forecastModel->uuid);
    }

    public function test_not_found(): void
    {
        $testerUser = User::factory()->create();
        $repository = new ForecastRepository($testerUser->id);

        $notFound = $repository->findFirst("abcdefjh");
        $this->assertNull($notFound);
    }

    public function test_find_all_activies_of_right_user(): void
    {
        $weatherResponseData = FakeWeatherResponseData::get();
        $weatherResponse = new OpenWeatherResponse($weatherResponseData);

        $testerUser1 = User::factory()->create();
        $repository1 = new ForecastRepository($testerUser1->id);

        $testerUser2 = User::factory()->create();
        $repository2 = new ForecastRepository($testerUser2->id);

        //creating 2 actives
        $repository1->save($weatherResponse);
        $repository1->save($weatherResponse);

        //creating 2 inactives
        $forecastModel3 = $repository1->save($weatherResponse);
        $forecastModel3->active = false;
        $forecastModel3->save();

        $forecastModel4 = $repository1->save($weatherResponse);
        $forecastModel4->active = false;
        $forecastModel4->save();

        //creating 2 actives to a second user
        $repository2->save($weatherResponse);
        $repository2->save($weatherResponse);

        //creating 2 inactives to a second user
        $forecastModel7 = $repository2->save($weatherResponse);
        $forecastModel7->active = false;
        $forecastModel7->save();

        $forecastModel8 = $repository2->save($weatherResponse);
        $forecastModel8->active = false;
        $forecastModel8->save();

        $activiesOfUser1 = $repository1->findAllActives();
        $this->assertEquals(count($activiesOfUser1), 2);
        $this->assertContainsOnlyInstancesOf(ForecastModel::class, $activiesOfUser1);

        $activiesOfUser2 = $repository2->findAllActives();
        $this->assertEquals(count($activiesOfUser2), 2);
        $this->assertContainsOnlyInstancesOf(ForecastModel::class, $activiesOfUser2);
    }
}
