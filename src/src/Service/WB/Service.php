<?php
declare(strict_types=1);

namespace App\Service\WB;

use Cake\Http\Client as HttpClient;
use Eggheads\CakephpClickHouse\ClickHouse;
use Eggheads\CakephpClickHouse\ClickHouseTransaction;

class Service implements ServiceInterface
{
    public function __construct(protected HttpClient $httpClient, protected ClickHouse $clickHouse)
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
        static $rowCount = 0;

        $response = $this->httpClient->get($searchUrl);

        $body = $response->getBody()->getContents();

        $body = json_decode($body, true);
        if ($body == null) {
            return;
        }

        $searchTableKeys = [
            'search_id',
            'search_word',
            'product_id',
            'product_name',
            'brand_name',
        ];

        try{
            $tx = new ClickHouseTransaction($this->clickHouse,'default.search_results', $searchTableKeys);
            foreach ($body['data']['products'] as $product) {
                $row = array_combine($searchTableKeys, [
                    $rowCount++,
                    $searchWord,
                    $product['id'],
                    $product['name'],
                    $product['brand']
                ]);
                $tx->append($row);
            }
            $tx->commit();
        }
        catch(\Throwable $e) {
            $tx->rollback();
            throw $e;
        }
    }

    public function searchByQueryWithPaging(string $query, int $page, int $perPage): array
    {
        $result = [];
        if ($query === '') {
            return [$result, 0];
        }

        $stmt = $this->clickHouse->select(
            "SELECT *
                        FROM search_results
                    WHERE search_word LIKE '%$query%' OR product_name LIKE '%$query%'
                    ORDER BY search_id
                    LIMIT $perPage OFFSET " . ($page - 1) * $perPage
        );

        foreach ($stmt as $row) {
            $result[] = $row;
        }

        $total = $stmt->countAll();

        return [$result, $total];
    }
}
