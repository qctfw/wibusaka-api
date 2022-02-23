<?php

namespace App\Http\Controllers;

use App\Enums\AnimeSource;
use App\Exceptions\RequestIdsExceededException;
use App\Http\Requests\AnimeResourceRequest;
use App\Http\Resources\MultipleAnimeResource;
use App\Services\Contracts\EntryServiceInterface;
use App\Services\Contracts\ResourceServiceInterface;
use Illuminate\Support\Collection;

class AnimeResourceController extends Controller
{
    public function __construct(
        private EntryServiceInterface $entry_service,
        private ResourceServiceInterface $resource_service
    )
    {
        $this->entry_service = $entry_service;
        $this->resource_service = $resource_service;
    }

    public function main(AnimeSource $source, AnimeResourceRequest $request)
    {
        $ids = explode(',', trim($request->input('id'), ','));
        $ids = array_unique($ids);

        if (count($ids) > config('wibusaka.max_id_per_request')) {
            throw new RequestIdsExceededException();
        }

        $ids = $this->entry_service->convert($ids, $source);

        $resources = $this->resource_service->get($ids->filter()->values()->toArray());

        $response = $this->prepareResponse($ids, $resources);

        return MultipleAnimeResource::collection($response);
    }

    private function prepareResponse(Collection|array $ids, Collection|array $resources): Collection
    {
        $response = collect();
        foreach ($ids as $id_from_request => $id)
        {
            $response->push([
                'id' => $id_from_request,
                'resources' => $resources[$id] ?? []
            ]);
        }

        return $response;
    }
}
