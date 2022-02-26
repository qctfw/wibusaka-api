<?php

namespace App\Services;

use App\Enums\AnimeSource;
use App\Models\Entry;
use App\Services\Contracts\EntryServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EntryService implements EntryServiceInterface
{
    public function convert(array|int $ids, AnimeSource $entry_from, AnimeSource $entry_to = AnimeSource::MyAnimeList): Collection
    {
        $result = collect($ids)->mapWithKeys(function ($id) {
            return [$id => intval($id)];
        });

        if ($entry_from === $entry_to) {
            return $result;
        }

        $processed_ids = collect();
        $request_ids = collect();

        $dead_ids = $this->getDeadID($ids, $entry_from);
        foreach ($dead_ids as $dead_id) {
            $processed_ids->push($dead_id);
            $result[$dead_id] = null;
        }

        $result_db = $this->convertFromDB($ids, $entry_from, $entry_to)->filter();
        $result_db->each(function ($id_to, $id_from) use ($processed_ids, $result) {
            $processed_ids->push($id_from);
            $result[$id_from] = $id_to;
        });

        foreach ($ids as $id_from => $id_to) {
            if ($processed_ids->search($id_from) === false) {
                $request_ids->push([$entry_from->value => $id_from]);
            }
        }

        if ($request_ids->isNotEmpty()) {
            $response_ids = collect();
            $http_response = Http::baseUrl('https://relations.yuna.moe')->acceptJson()->asJson()->post('api/ids', $request_ids);

            foreach ($http_response->json() as $response_index => $response_id) {
                $id_from = (! is_null($response_id)) ? $response_id[$entry_from->value] : $request_ids[$response_index][$entry_from->value];
                $id_to = (! is_null($response_id)) ? $response_id[$entry_to->value] : null;

                $result[$id_from] = $id_to;

                $response_ids->put($id_from, $id_to);
            }

            $this->insertToDB($response_ids, $entry_from, $entry_to);
        }

        return $result;
    }

    public function convertFromDB(array|int $ids, AnimeSource $entry_from, AnimeSource $entry_to = AnimeSource::MyAnimeList)
    {
        if (is_integer($ids)) {
            $ids = [$ids];
        }

        $column_from = $entry_from->value . '_id';
        $column_to = $entry_to->value . '_id';

        $entries = Entry::whereIn($column_from, $ids)->get([$column_from, $column_to]);

        $result = collect();
        foreach ($ids as $id) {
            $entry = $entries->where($column_from, $id)->first();

            $result->put($id, $entry?->getOriginal($column_to));
        }

        return $result;
    }

    private function insertToDB(Collection|array $ids, AnimeSource $entry_from, AnimeSource $entry_to = AnimeSource::MyAnimeList)
    {
        $entries = collect();
        foreach ($ids as $id_from => $id_to) {
            if (is_null($id_to)) {
                Cache::tags(['db', 'dead-entries', 'dead-' . $entry_from->value . '-id'])->put($id_from, true);
            } else {
                $entries->push([
                    $entry_from->value . '_id' => $id_from,
                    $entry_to->value . '_id' => $id_to,
                ]);
            }
        }

        return ($entries->isNotEmpty()) ? Entry::insertOrIgnore($entries->toArray()) : 0;
    }

    private function getDeadID(array|int $ids, AnimeSource $entry_from)
    {
        if (is_integer($ids)) {
            $ids = [$ids];
        }

        $dead_ids = collect();
        foreach ($ids as $id) {
            $is_dead = Cache::tags(['db', 'dead-entries', 'dead-' . $entry_from->value . '-id'])->get($id);
            if ($is_dead === true) {
                $dead_ids->push($id);
            }
        }

        return $dead_ids;
    }
}
