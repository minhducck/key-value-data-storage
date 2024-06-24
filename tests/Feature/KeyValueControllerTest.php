<?php

namespace Minhducck\KeyValueDataStorage\Test\Feature;

use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Minhducck\KeyValueDataStorage\Providers\KeyValueDataObjectServiceProvider;
use Minhducck\KeyValueDataStorage\Services\KeyValueStorageService;
use Mockery\MockInterface;
use Orchestra\Testbench\TestCase;

class KeyValueControllerTest extends TestCase
{
    protected function setUp(): void
    {
        putenv('KEY_VALUE_STORAGE.ENABLE=1');
        putenv('KEY_VALUE_STORAGE.RESTRICT_READ_PERMISSION=0');
        putenv('KEY_VALUE_STORAGE.RESTRICT_WRITE_PERMISSION=0');
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [KeyValueDataObjectServiceProvider::class];
    }

    public function createApplication()
    {
        $app = parent::createApplication();
        $app->loadEnvironmentFrom('.env.testing');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        Artisan::call('migrate');

        return $app;
    }

    public function testGetAllRecordsOnEmptyResponse()
    {
        $this->mock(KeyValueStorageService::class, function ($mock) {
            $mock->shouldReceive('getAllRecords')
                ->andReturn([]);
        });

        $response = $this->get(
            '/object/get_all_records',
            [
                'content-type' => 'application/json',
                'accepts'      => 'application/json',
            ]
        );
        $response->assertJsonIsArray();
        $response->assertExactJson([]);
        $response->assertContent('{}');
    }

    public function testGetAllRecords()
    {
        $response = $this->get(
            '/object/get_all_records',
            [
                'content-type' => 'application/json',
                'accepts'      => 'application/json',
            ]
        );
        $data = $response->json();
        $response->assertOk();
        $encodedData = json_encode($data);

        // $response->assertJsonIsObject() is missing case empty array then using some manual checks.
        $this->assertTrue(
            (is_array($data) && empty($data))
            || (is_array($data) && str_starts_with($encodedData, '{') && str_ends_with($encodedData, '}'))
        );
    }

    private function _processPostRequest(array $data, Carbon $timeEvent): void
    {
        // Start Post Request
        Carbon::setTestNow($timeEvent);
        $response = $this->postJson(
            '/object',
            $data,
            ['accepts' => 'application/json']
        );

        $response->assertJsonIsObject();
        $response->assertOk();
        $parseJson = json_decode($response->getContent(), true);

        $this->assertIsArray($parseJson);
        $this->assertArrayHasKey('time', $parseJson);
        $this->assertSame($timeEvent->toIso8601String(), $parseJson['time']);
    }

    private function _processGetValueByKey(
        string $key,
        mixed $expectedValue,
        null|int|string $timestamp = null
    ): void
    {
        $url = $timestamp ? "/object/{$key}?timestamp={$timestamp}" : "/object/{$key}";
        $retrieveMyKey = $this->get($url);
        $retrieveMyKey->assertJsonIsObject();
        $retrieveMyKey->assertJsonPath($key, $expectedValue);
    }

    public function testFullFlowWriteReadAndRetrieveVersioned()
    {
        $timestampBaseLine = rand(10000000, 17000000);
        $firstEventTime = Carbon::createFromTimestamp($timestampBaseLine+10000);
        $secondEventTime = Carbon::createFromTimestamp($timestampBaseLine+20000);
        $readEventTimeInMiddle = Carbon::createFromTimestamp($timestampBaseLine+10500);

        // First write event
        $this->_processPostRequest(['mykey' => 'value1'], $firstEventTime);

        // Retrieve Key
        $this->_processGetValueByKey('mykey', 'value1');

        // Second write event
        $this->_processPostRequest(['mykey' => 'value2'], $secondEventTime);

        // Retrieve Key
        $this->_processGetValueByKey('mykey', 'value2');

        // Retrieve Key
        $this->_processGetValueByKey(
            'mykey',
            'value1',
            $readEventTimeInMiddle->getTimestamp()
        );
    }

    public function testSaveOnCorruptedPostInput()
    {
        $this->mock(\Illuminate\Http\Request::class, function(MockInterface $mock) {
            $mock->shouldReceive('post')->andReturn('CORUPTED_DATA');
        });
        $response = $this->postJson('/object', [], ['accepts' => 'application/json']);
        $response->assertStatus(400);
    }

    public function testGetKeyOnNonValidKey()
    {
        $key = 'some_random_key_of_course_not_available';
        $response = $this->getJson("/object/{$key}");
        $response->assertNotFound();
    }
}