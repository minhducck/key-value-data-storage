<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Services;

use Carbon\Carbon;
use DateTime;
use Minhducck\KeyValueDataStorage\Exceptions\InvalidInputException;
use Minhducck\KeyValueDataStorage\Exceptions\UnableToSaveException;
use Minhducck\KeyValueDataStorage\Helpers\DataMapper;
use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;
use Minhducck\KeyValueDataStorage\Interfaces\KeyValueStorageServiceInterface;
use Minhducck\KeyValueDataStorage\Models\KeyValue;
use Minhducck\KeyValueDataStorage\Models\KeyValueChangelog;
use Minhducck\KeyValueDataStorage\Models\TableConstant;

class KeyValueStorageService implements KeyValueStorageServiceInterface
{

    /**
     * Create Datetime object from $timestamp datatype.
     *
     * @param int|string|null $timestamp
     * @return DateTime
     */
    private function createQueryTime(int|string|null $timestamp): DateTime
    {
        if (!$timestamp) {
            return Carbon::now()->toDate();
        }

        if (is_numeric($timestamp)) {
            return Carbon::createFromTimestampUTC($timestamp)->toDate();
        }

        return Carbon::createFromTimeString($timestamp)->toDate();
    }

    public function retrieve(string $key, int|string|null $timestamp = null): array
    {
        if ($timestamp === null) {
            $data = KeyValue::where(TableConstant::TABLE_FIELD_KEY, '=', $key)->firstOrFail();
            return DataMapper::fromInstanceToDictionary($data);
        }

        $queryDateTime = $this->createQueryTime($timestamp);
        $foundItem     = KeyValueChangelog::where(TableConstant::TABLE_FIELD_KEY, '=', $key)
            ->where(TableConstant::TABLE_FIELD_TIMESTAMP, '<=', $queryDateTime)
            ->orderByDesc(TableConstant::TABLE_FIELD_TIMESTAMP)
            ->firstOrFail();

        return DataMapper::fromInstanceToDictionary($foundItem);
    }

    /**
     * @throws InvalidInputException
     * @throws UnableToSaveException
     */
    public function save(array $dataObjects): int
    {
        $currentTimestamp       = Carbon::now()->timestamp;
        $keyValueModels         = DataMapper::dictionaryToKeyValueInstances($dataObjects, (int)$currentTimestamp);
        $dataBaseSavableObjects = array_map(
            fn(KeyValueDataObjectInterface $instance) => $instance->serialize(),
            $keyValueModels
        );

        if (empty($keyValueModels)) {
            throw new InvalidInputException('Unable to save empty objects');
        }

        $result = KeyValue::upsert(
            $dataBaseSavableObjects,
            uniqueBy: [KeyValueDataObjectInterface::KEY],
            update: [
                KeyValueDataObjectInterface::VALUE,
                KeyValueDataObjectInterface::TIMESTAMP,
                KeyValueDataObjectInterface::METADATA,
            ]
        );

        if (!$result) {
            throw new UnableToSaveException('Unable to save key-values.');
        }

        return (int)$currentTimestamp;
    }

    public function getAllRecords(): array
    {
        $allKey   = KeyValue::all();
        $response = [];

        foreach ($allKey->getIterator() as $keyValueObject) {
            $response += DataMapper::fromInstanceToDictionary($keyValueObject);
        }

        return $response;
    }
}
