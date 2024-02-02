<?php

namespace App\Test\Service\WB;

use App\Service\WB\Service;
use Cake\Http\Client as HttpClient;
use ClickHouseDB\Client as ClickHouseClient;
use ClickHouseDB\Statement;
use ClickHouseDB\Transport\CurlerRequest;
use Eggheads\CakephpClickHouse\ClickHouse;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    public function testStoreBySearchWord()
    {
        $responseJSON =
<<<EOL
{
    "data": {
        "products": [
            {
              "id": 14402603,
              "name": "Средство для мытья посуды Нежные руки Розовый жасмин 450 мл",
              "brand": "Fairy"
            }
        ]
    }
}
EOL;
        $httpClientMock = $this->createMock(HttpClient::class);
        $httpClientMock
            ->expects($this->atLeastOnce())
            ->method('get')
            ->willReturn(new HttpClient\Response([],$responseJSON));

        $clickHouseClientMock = $this->createMock(ClickHouseClient::class);
        $clickHouseClientMock
            ->expects($this->atLeastOnce())
            ->method('insertBatchFiles')
            ->willReturn([new Statement(new CurlerRequest())]);
        ;

        $wbService = new Service($httpClientMock, new ClickHouse($clickHouseClientMock));

        $wbService->storeBySearchWord('searchWord');
    }

    public function testSearchByQueryWithPaging()
    {
        $statementLikeDummyObject = new class implements \Iterator {
            public function current(): mixed
            {
                return false;
            }

            public function next(): void
            {
            }

            public function key(): mixed
            {
                return false;
            }

            public function valid(): bool
            {
                return false;
            }

            public function rewind(): void
            {
            }

            public function countAll() {
                return 0;
            }
        };

        $httpClientMock = $this->createMock(HttpClient::class);
        $clickHouseClientMock = $this->createMock(ClickHouseClient::class);
        $clickHouseClientMock
            ->expects($this->atLeastOnce())
            ->method('select')
            ->willReturn($statementLikeDummyObject)
        ;

        $wbService = new Service($httpClientMock, $clickHouseClientMock);

        $wbService->searchByQueryWithPaging('searchString', 1, 100);
    }
}
