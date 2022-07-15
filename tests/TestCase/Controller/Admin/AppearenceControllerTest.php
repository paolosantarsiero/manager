<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AppearenceController;
use App\Controller\Component\ConfigComponent;
use BEdita\SDK\BEditaClient;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\AppearenceController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\AppearenceController
 */
class AppearenceControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\AppearenceController
     */
    public $Appearence;

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
        Cache::disable();
        parent::tearDown();
    }

    /**
     * Test initialize
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $this->Appearence = new AppearenceController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ]
            )
        );
        static::assertNotEmpty($this->Appearence->Config);
        static::assertInstanceOf(ConfigComponent::class, $this->Appearence->Config);
    }

    /**
     * Test index
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->Appearence = new AppearenceController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ]
            )
        );
        $this->Appearence->index();
        $viewVars = (array)$this->Appearence->viewBuilder()->getVars();
        static::assertNotEmpty($viewVars['configs']['modules']);
        static::assertNotEmpty($viewVars['configs']['pagination']);
        static::assertArrayHasKey('alert_message', $viewVars['configs']);
        static::assertArrayHasKey('export', $viewVars['configs']);
        static::assertArrayHasKey('modules', $viewVars['configs']);
        static::assertArrayHasKey('pagination', $viewVars['configs']);
        static::assertArrayHasKey('project', $viewVars['configs']);
        static::assertArrayHasKey('properties', $viewVars['configs']);
    }

    /**
     * Test save
     *
     * @return void
     * @covers ::save()
     */
    public function testSave(): void
    {
        $this->Appearence = new AppearenceController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'post' => [
                        'Properties' => '[]',
                        'property_name' => 'properties',
                    ],
                ]
            )
        );
        // mock GET /config and /admin/applications.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->will(
                $this->returnCallback(
                    function ($param) {
                        if ($param === '/config') {
                            return [];
                        }
                        if ($param === '/admin/applications') {
                            return [
                                'data' => [['id' => 123456789, 'attributes' => ['name' => 'manager']]],
                            ];
                        }
                    }
                )
            );
        // set $this->Modules->Config->apiClient
        $property = new \ReflectionProperty(ConfigComponent::class, 'apiClient');
        $property->setAccessible(true);
        $property->setValue($this->Appearence->Config, $apiClient);
        // expect exception on redirect to admin uri, because test does not access admin routes as unauthenticated
        $this->expectException('Cake\Routing\Exception\MissingRouteException');
        $this->Appearence->save();
    }
}
