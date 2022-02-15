<?php

namespace App\Services;

use App\Models\Resource;
use App\Services\Contracts\ResourceServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ResourceService implements ResourceServiceInterface
{
    public function get(array|int $mal_ids): Collection
    {
        if (is_integer($mal_ids))
        {
            $mal_ids = array($mal_ids);
        }

        $resources_result = collect();
        $mal_ids_db = collect();

        foreach ($mal_ids as $mal_id) {
            $resources_cache = $this->getFromCache($mal_id);

            if (!is_null($resources_cache)) {
                $resources_result->put($mal_id, $resources_cache);
            }
            else {
                $mal_ids_db->push($mal_id);
            }
        }

        if ($mal_ids_db->isNotEmpty()) {
            $resources_db = Resource::with('platform')->byMalId($mal_ids_db->toArray())->get();

            foreach ($mal_ids_db as $mal_id) {
                $resources = $resources_db->where('mal_id', $mal_id)->sortBy('platform.name', SORT_NATURAL | SORT_FLAG_CASE)->values();

                $resources_result->put($mal_id, $resources);

                $this->setToCache($resources, $mal_id);
            }
        }

        return $resources_result;
    }

    private function getFromCache(int $mal_id)
    {
        $cache_key = $this->getCacheKey($mal_id);

        $cache = Cache::tags(['db', 'db-anime-resources'])->get($cache_key);

        return $cache;
    }

    private function setToCache($resources, int $mal_id)
    {
        $cache_key = $this->getCacheKey($mal_id);

        Cache::tags(['db', 'db-anime-resources'])->put($cache_key, $resources);
    }

    private function getCacheKey(int $mal_id)
    {
        return 'db-anime-resources-' . $mal_id;
    }
}
