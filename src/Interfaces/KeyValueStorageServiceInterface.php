<?php
declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Interfaces;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

interface KeyValueStorageServiceInterface
{
    /**
     * @api GET /object/get_all_records
     * @return array<string, mixed>
     */
    public function getAllRecords(): array;

    /**
     * @api GET /object/:myKey
     * @param string   $key
     * @param int|null|string $timestamp
     * @return array<string, mixed>
     */
    public function retrieve(string $key, null|string|int $timestamp): array;

    /**
     * @param array<string, mixed> $dataObjects
     * @return int last save event timestamp
     */
    public function save(array $dataObjects): int;
}
