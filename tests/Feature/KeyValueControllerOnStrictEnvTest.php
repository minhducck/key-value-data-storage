<?php

namespace Minhducck\KeyValueDataStorage\Test\Feature;

use Illuminate\Support\Facades\Artisan;
use Minhducck\KeyValueDataStorage\Providers\KeyValueDataObjectServiceProvider;
use Orchestra\Testbench\TestCase;

class KeyValueControllerOnStrictEnvTest extends TestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        $app->loadEnvironmentFrom('.env.testing');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        Artisan::call('migrate');

        return $app;
    }

    protected function setUp(): void
    {
        putenv('KEY_VALUE_STORAGE.ENABLE=1');
        putenv('KEY_VALUE_STORAGE.RESTRICT_READ_PERMISSION=1');
        putenv('KEY_VALUE_STORAGE.RESTRICT_WRITE_PERMISSION=1');
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [KeyValueDataObjectServiceProvider::class];
    }

    public function testGetOnUnauthorized()
    {
        $response = $this->getJson("/object/sample_key");
        $response->assertUnauthorized();
    }

    public function testGetAllOnUnauthorized()
    {
        $response = $this->getJson("/object/get_all_records");
        $response->assertUnauthorized();
    }

    public function testPostOnUnauthorized()
    {
        $response = $this->postJson("/object", ['unAuth' => 'access']);
        $response->assertUnauthorized();
    }
}
