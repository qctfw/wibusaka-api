<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface EntryServiceInterface
{
    public function convert(array|int $ids, string $entry_from, string $entry_to = 'myanimelist'): Collection;
}
