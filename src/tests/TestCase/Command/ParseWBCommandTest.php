<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Service\WB\Service;
use App\Service\WB\ServiceInterface as WbServiceInterface;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ParseWBCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function testWbServiceIsInvoked(): void
    {
        $this->mockService(WbServiceInterface::class, function () {
            $searchService = $this->createMock(Service::class);
            $searchService->expects($this->once())->method('storeBySearchWord')
                ->with($this->equalTo('search_word'));
            return $searchService;
        });

        $this->exec('parse_w_b "search_word"');

        $this->assertExitSuccess();
        $this->assertOutputContains('Parsing WB finished');
    }
}
