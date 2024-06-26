<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Interfaces;

use Minhducck\KeyValueDataStorage\Exceptions\KeyNotFoundException;

interface KeyValueStorageServiceInterface
{
    /**
     * @api GET /object/get_all_records
     *
     * @return array<string, mixed>
     */
    public function getAllRecords(): array;

    /**
     * @api GET /object/:myKey
     *
     * @return array<string, mixed>
     * @throws KeyNotFoundException
     */
    public function retrieve(string $key, null|string|int $timestamp): array;

    /**
     * @param  array<string, mixed>  $dataObjects
     * @return int last save event timestamp
     */
    public function save(array $dataObjects): int;
}
