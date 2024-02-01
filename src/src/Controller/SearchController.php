<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\WB\ServiceInterface;
use Cake\Datasource\Paging\PaginatedResultSet;
use Cake\Http\Response;

class SearchController extends AppController
{
    public function search(ServiceInterface $wbService) : ?Response
    {
        $query = (string) $this->request->getQuery('query');
        $page = (int) $this->request->getQuery('page', 1);
        $perPage = (int) $this->request->getQuery('perPage', 20);

        [$results, $total] = $wbService->searchByQueryWithPaging($query, $page, $perPage);

        $pageCount = ceil($total / $perPage);
        $hasPrevPage = $page > 1;
        $hasNextPage = $page < $pageCount;

        $results = new PaginatedResultSet(new \ArrayIterator($results), [
            'count' => count($results),
            'totalCount' => $total,
            'perPage' => $perPage,
            'pageCount' => $pageCount,
            'currentPage' => $page,
            'hasPrevPage' => $hasPrevPage,
            'hasNextPage' => $hasNextPage,
        ]);

        $this->set(compact('results', 'query'));

        return $this->render();
    }
}
