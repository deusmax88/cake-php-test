<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Test\TestCase\Controller;

use App\Service\WB\Service;
use App\Service\WB\ServiceInterface as WbServiceInterface;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * PagesControllerTest class
 *
 * @uses \App\Controller\PagesController
 */
class SearchControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * testDisplay method
     *
     * @return void
     */
    public function testEmptySearch()
    {
        Configure::write('debug', true);
        $this->get('/search');
        $this->assertResponseOk();
        $this->assertResponseContains('search');
        $this->assertResponseContains('No record found.');
        $this->assertResponseContains('<html>');
    }

    public function testSearchWithResults()
    {
        Configure::write('debug', true);

        $this->mockService(WbServiceInterface::class, function () {
            $searchService = $this->createStub(Service::class);
            $searchService
                ->method('searchByQueryWithPaging')
                ->willReturn([[
                        [
                            'search_id' => 1,
                            'search_word' => 'test_search_word',
                            'product_id' => 1,
                            'product_name' => 'test_product_1',
                            'brand_name' => 'test_brand_1',
                        ],
                        [
                            'search_id' => 2,
                            'search_word' => 'test_search_word',
                            'product_id' => 2,
                            'product_name' => 'test_product_2',
                            'brand_name' => 'test_brand_2',
                        ],
                        [
                            'search_id' => 3,
                            'search_word' => 'test_search_word',
                            'product_id' => 3,
                            'product_name' => 'test_product_3',
                            'brand_name' => 'test_brand_3',
                        ]
                    ], 3
                ]);

            return $searchService;
        });

        $this->get('/search?query=test_product');
        $this->assertResponseOk();
        $this->assertResponseContains('test_brand_1');
        $this->assertResponseContains('test_product_1');
        $this->assertResponseContains('<html>');
    }
}
