<?php

namespace Tests\Feature;

use App\Enums\AnimeSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourcesAnimeV1Test extends TestCase
{
    use RefreshDatabase;

    /**
     * Test for successfully getting one resource from one ID.
     *
     * @return void
     */
    public function test_get_one_resource()
    {
        $id_from = AnimeSource::MyAnimeList;
        $id = rand(1, 10000);

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . $id);

        $response
                ->assertSuccessful()
                ->assertJson(fn (AssertableJson $json) => $json
                        ->has('data', 1, fn (AssertableJson $json) => $json
                                ->hasAll('id', 'resources')
                                ->where('id', $id)
                        )
                );
    }

    /**
     * Test for successfully getting one resource from one Anilist ID.
     *
     * @return void
     */
    public function test_get_one_resource_anilist()
    {
        $id_from = AnimeSource::Anilist;
        $id = rand(1, 10000);
        Http::fake([
            'https://relations.yuna.moe/api/ids' => $this->fakeHttpEntryRelations([$id], $id_from->value),
        ]);

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . $id);

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                    ->has('data', 1, fn (AssertableJson $json) => $json
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
            ->assertJson(fn (AssertableJson $json) => $json
                    ->has('data', count($ids), fn (AssertableJson $json) => $json
                            ->hasAll('id', 'resources')
                            ->where('id', $ids[0])
                    )
            );
    }

    /**
     * Test for successfully getting multiple (more than 1) resources from multiple IDs.
     *
     * @return void
     */
    public function test_get_multiple_resources_anilist()
    {
        $id_from = AnimeSource::Anilist;
        $ids = $this->generateRandomIds(rand(4, config('wibusaka.max_id_per_request')));
        Http::fake([
            'https://relations.yuna.moe/api/ids' => $this->fakeHttpEntryRelations($ids, $id_from->value),
        ]);

        $response = $this->get('/v1/resources/anime/' . $id_from->value . '?id=' . implode(',', $ids));

        $response
            ->assertSuccessful()
            ->assertJson(fn (AssertableJson $json) => $json
                    ->has('data', count($ids), fn (AssertableJson $json) => $json
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
            ->assertJson(fn (AssertableJson $json) => $json
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
            ->assertJson(fn (AssertableJson $json) => $json
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
            ->assertJson(fn (AssertableJson $json) => $json
                    ->has('data', count($unique_ids), fn (AssertableJson $json) => $json
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
            $id = rand(1, 10000);
            if (array_search($id, $ids) === false) {
                $ids[] = $id;
                $i++;
            }
        } while ($i <= $max);

        return $ids;
    }

    /**
     * Generate Fake HTTP Entry Relations for test.
     *
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    private function fakeHttpEntryRelations(array $ids, string $source_from)
    {
        $responses = [];
        foreach ($ids as $id) {
            $responses[] = [
                $source_from => $id,
                'myanimelist' => $id,
            ];
        }

        return Http::response($responses);
    }
}
