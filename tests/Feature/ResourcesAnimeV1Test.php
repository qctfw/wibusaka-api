<?php

namespace Tests\Feature;

use App\Enums\AnimeSource;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourcesAnimeV1Test extends TestCase
{
    /**
     * Test for successfully getting one resource from one ID.
     *
     * @return void
     */
    public function test_get_one_resource()
    {
        $id_from = AnimeSource::MyAnimeList;
        $id = rand(1, 50000);

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . $id);

        $response
                ->assertSuccessful()
                ->assertJson(fn (AssertableJson $json) =>
                    $json
                        ->has('data', 1, fn (AssertableJson $json) =>
                            $json
                                ->hasAll('id', 'resources')
                                ->where('id', $id)
                        )
                );
    }

    /**
     * Test for successfully getting multiple (more than 1) resources from multiple IDs.
     *
     * @return void
     */
    public function test_get_multiple_resources()
    {
        $id_from = AnimeSource::MyAnimeList;
        $ids = $this->generateRandomIds(rand(4, config('wibusaka.max_id_per_request')));

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . implode(',', $ids));
        
        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data', count($ids), fn (AssertableJson $json) =>
                        $json
                            ->hasAll('id', 'resources')
                            ->where('id', $ids[0])
                    )
            );
    }

    /**
     * Test for validating route request.
     *
     * @return void
     */
    public function test_validate_parameter_request()
    {
        $id_from = AnimeSource::MyAnimeList;
        $id = '-1,';

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . $id);

        $response
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->where('type', 'ValidationException')
                    ->etc()
            );
    }

    /**
     * Test for validating maximum IDs that are allowed at one-time request.
     *
     * @return void
     */
    public function test_validate_maximum_id_request()
    {
        // Preparation
        $id_from = AnimeSource::MyAnimeList;
        $maximum = config('wibusaka.max_id_per_request');

        $ids = $this->generateRandomIds($maximum + rand(2, 10));

        // Action
        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . implode(',', $ids));

        // Assertion
        $response
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->where('type', 'RequestIdsExceededException')
                    ->etc()
            );
    }

    /**
     * Test for repeated IDs and returns only one resource for that repeated IDs.
     *
     * @return void
     */
    public function test_repeated_id_request()
    {
        $id_from = AnimeSource::MyAnimeList;
        $ids = [11111, 11111, 11, 111, 1111, 11, 1, 111, 22, 222, 2, 2, 222, 2, 22222, 333, 33, 3, 33, 333, 3333];
        $unique_ids = array_unique($ids);

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . implode(',', $ids));

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) =>
                $json
                    ->has('data', count($unique_ids), fn (AssertableJson $json) =>
                        $json
                            ->hasAll('id', 'resources')
                            ->where('id', $ids[0])
                    )
            );
    }

    /**
     * Generate Random IDs.
     *
     * @return array
     */
    private function generateRandomIds(int $max): array
    {
        $ids = [];
        $i = 1;
        do {
            $id = rand(1, 50000);
            if (array_search($id, $ids) === false) {
                $ids[] = $id;
                $i++;
            }
        } while ($i <= $max);

        return $ids;
    }
}
