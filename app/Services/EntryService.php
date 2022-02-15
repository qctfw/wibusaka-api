<?php

namespace App\Services;

use App\Services\Contracts\EntryServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class EntryService implements EntryServiceInterface
{
    public function convert(array|int $ids, string $entry_from, string $entry_to = 'myanimelist'): Collection
    {
        $request_ids = collect();
        $response_ids = collect();

        // TODO: Get from db/cache or fetch the requests if not available

        if (is_integer($ids))
        {
            $ids = array($ids);
        }

        foreach ($ids as $id) {
            $request_ids->push([$entry_from => $id]);
        }

        $http_response = Http::baseUrl('https://relations.yuna.moe')->acceptJson()->asJson()->post('api/ids', $request_ids);

        foreach ($http_response->json() as $response_index => $response_id) {
            // TODO: Handle invalid ID
            $id_from = (!is_null($response_id)) ? $response_id[$entry_from] : $request_ids[$response_index];
            $id_to = (!is_null($response_id)) ? $response_id[$entry_to] : null;

            $response_ids->put($id_from, $id_to);
        }

        return $response_ids;
    }
}
