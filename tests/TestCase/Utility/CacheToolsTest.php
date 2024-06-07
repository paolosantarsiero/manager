<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase;

use App\Utility\CacheTools;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\CacheTools Test Case
 *
 * @coversDefaultClass App\Utility\CacheTools
 */
class CacheToolsTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        Cache::enable();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Cache::disable();
    }

    /**
     * Test `cacheKey` method.
     *
     * @return void
     * @covers ::cacheKey()
     */
    public function testCacheKey(): void
    {
        $apiSignature = md5(ApiClientProvider::getApiClient()->getApiBaseUrl());
        $expected = sprintf('%s_%s', 'test', $apiSignature);
        $actual = CacheTools::cacheKey('test');
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getModuleCount` method.
     *
     * @return void
     * @covers ::getModuleCount()
     */
    public function testGetModuleCount(): void
    {
        $moduleName = 'test';
        $cacheKey = sprintf('statistics_%s_count', $moduleName);
        $count = 42;
        Cache::write($cacheKey, $count);
        $expected = $count;
        $actual = CacheTools::getModuleCount($moduleName);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `setModuleCount` method.
     *
     * @return void
     * @covers ::setModuleCount()
     * @covers ::getModuleCount()
     */
    public function testSetModuleCount(): void
    {
        $moduleName = 'test';
        $response = [
            'meta' => [
                'pagination' => [
                    'count' => 42,
                ],
            ],
        ];
        CacheTools::setModuleCount($response, $moduleName);
        $expected = 42;
        $actual = CacheTools::getModuleCount($moduleName);
        static::assertEquals($expected, $actual);
    }
}
