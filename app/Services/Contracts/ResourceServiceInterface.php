<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface ResourceServiceInterface
{
    public function get(array|int $mal_ids): Collection;
}
