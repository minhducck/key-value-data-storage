<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;
use Minhducck\KeyValueDataStorage\Interfaces\KeyValueStorageServiceInterface;
use Minhducck\KeyValueDataStorage\Models\KeyValue;
use Minhducck\KeyValueDataStorage\Services\KeyValueStorageService;

final class KeyValueDataObjectServiceProvider extends ServiceProvider
{
    /** @var string[] */
    public array $bindings = [
        KeyValueDataObjectInterface::class => KeyValue::class,
    ];

    /** @var string[] */
    public array $singletons = [
        KeyValueStorageServiceInterface::class => KeyValueStorageService::class,
    ];

    /**
     * @param string[] $paths
     * @return string
     */
    private function resolvePath(array $paths = []): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_merge([__DIR__], $paths)
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        if (!env('KEY_VALUE_STORAGE.ENABLE', 0)) {
            $this->singletons = [];
            $this->bindings   = [];
            return;
        }

        $this->loadMigrationsFrom($this->resolvePath(['..', 'database', 'migrations']));
        $this->loadRoutesFrom($this->resolvePath(['..', 'routes', 'api.php']));
    }
}
