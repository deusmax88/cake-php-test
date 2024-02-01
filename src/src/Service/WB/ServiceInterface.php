<?php
declare(strict_types=1);

namespace App\Service\WB;

interface ServiceInterface
{
    public function storeBySearchWord(string $searchWord);

    public function searchByQueryWithPaging(string $query, int $page, int $perPage);
}
