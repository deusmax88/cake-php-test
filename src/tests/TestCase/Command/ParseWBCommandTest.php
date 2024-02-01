<?php
declare(strict_types=1);

namespace App\Test\TestCase\Command;

use App\Command\ParseWBCommand;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Command\ParseWBCommand Test Case
 *
 * @uses \App\Command\ParseWBCommand
 */
class ParseWBCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @uses \App\Command\ParseWBCommand::buildOptionParser()
     */
    public function testBuildOptionParser(): void
    {
        $this->markTestSkipped();
    }

    /**
     * Test execute method
     *
     * @return void
     * @uses \App\Command\ParseWBCommand::execute()
     */
    public function testExecute(): void
    {
        $this->markTestSkipped();
    }
}
