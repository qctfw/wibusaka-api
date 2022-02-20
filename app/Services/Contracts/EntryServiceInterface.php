<?php

namespace App\Services\Contracts;

use App\Enums\AnimeSource;
use Illuminate\Support\Collection;

interface EntryServiceInterface
{
    public function convert(array|int $ids, AnimeSource $entry_from, AnimeSource $entry_to = AnimeSource::MyAnimeList): Collection;
}
