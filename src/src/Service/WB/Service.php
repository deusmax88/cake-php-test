<?php
declare(strict_types=1);

namespace App\Service\WB;

use Cake\Http\Client as HttpClient;
use ClickHouseDB\Client as ClickHouseClient;

class Service implements ServiceInterface
{
    public function __construct(protected HttpClient $httpClient, protected ClickHouseClient $clickHouseClient)
    {
    }

    public function storeBySearchWord(string $searchWord): void
    {
        $wordForQuery = urlencode($searchWord);

        $searchUrl = 'https://search.wb.ru/exactmatch/ru/common/v4/search?TestGroup=no_test&TestID=no_test&appType=1'
            . '&curr=rub&dest=-1255942&query='
            . $wordForQuery
            . '&regions=80,38,4,64,83,33,68,70,69,30,86,75,40,1,66,110,22,31,48,71,114&resultset=catalog&sort=popular&spp=0&suppressSpellcheck=false';

        // Making http requests
        for ($i = 1; $i < 12; $i++) {
            $this->doRequestAndStoreResults($searchWord,$searchUrl."&page=".$i);
        }
    }

    private function doRequestAndStoreResults(string $searchWord, string $searchUrl) : void
    {
        $response = $this->httpClient->get($searchUrl);

        $body = $response->getBody()->getContents();

        $body = json_decode($body, true);
        if ($body == null) {
            return;
        }

        foreach ($body['data']['products'] as $product) {
            $row = [
                'search_id' => 1,
                'search_word' => $searchWord,
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'brand_name' => $product['brand']
            ];

            $this->clickHouseClient->insertAssocBulk('search_results', $row);
        }
    }

    public function searchByQueryWithPaging(string $query, int $page, int $perPage): array
    {
        $result = [];
        if ($query === '') {
            return [$result, 0];
        }

        $stmt = $this->clickHouseClient->select(
            "SELECT *
                        FROM search_results
                    WHERE search_word LIKE '%$query%' OR product_name LIKE '%$query%'
                    ORDER BY product_id
                    LIMIT $perPage OFFSET " . ($page - 1) * $perPage
        );

        foreach ($stmt as $row) {
            $result[] = $row;
        }

        $total = $stmt->countAll();

        return [$result, $total];
    }
}
