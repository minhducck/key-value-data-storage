<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Services;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Minhducck\KeyValueDataStorage\Exceptions\InvalidInputException;
use Minhducck\KeyValueDataStorage\Exceptions\KeyNotFoundException;
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
     */
    private function createQueryTime(int|string|null $timestamp): DateTime
    {
        if (! $timestamp) {
            return Carbon::now()->toDate();
        }

        if (is_numeric($timestamp)) {
            return Carbon::createFromTimestampUTC($timestamp)->toDate();
        }

        return Carbon::createFromTimeString($timestamp)->toDate();
    }

    /**
     * @throws KeyNotFoundException
     */
    public function retrieve(string $key, int|string|null $timestamp = null): array
    {
        try {
            if ($timestamp === null) {
                $data = KeyValue::where(TableConstant::TABLE_FIELD_KEY, '=', $key)->firstOrFail();

                return DataMapper::fromInstanceToDictionary($data);
            }

            $queryDateTime = $this->createQueryTime($timestamp);
            $foundItem = KeyValueChangelog::where(TableConstant::TABLE_FIELD_KEY, '=', $key)
                ->where(TableConstant::TABLE_FIELD_TIMESTAMP, '<=', $queryDateTime)
                ->orderByDesc(TableConstant::TABLE_FIELD_TIMESTAMP)
                ->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw new KeyNotFoundException(sprintf('There is no value for "%s" key.', $key));
        }

        return DataMapper::fromInstanceToDictionary($foundItem);
    }

    /**
     * @throws InvalidInputException
     * @throws UnableToSaveException
     */
    public function save(array $dataObjects): int
    {
        $currentTime = Carbon::now();
        $currentTimestamp = $currentTime->timestamp;
        $keyValueModels = DataMapper::dictionaryToKeyValueInstances($dataObjects, (int) $currentTimestamp);
        $dataBaseSavableObjects = array_map(
            fn (KeyValueDataObjectInterface $instance) => $instance->serialize(),
            $keyValueModels
        );

        if (empty($keyValueModels)) {
            throw new InvalidInputException('Unable to save empty objects');
        }

        try {
            [$rawQueryString, $binding] = $this->prepareInsertQuery($dataBaseSavableObjects, $currentTime);
            $result = DB::statement($rawQueryString, $binding);
        } catch (\Illuminate\Database\QueryException $exception) {
            throw new UnableToSaveException('Unable to save key-values.', $exception);
        }

        if (! $result) {
            throw new UnableToSaveException('Unable to save key-values.');
        }

        return (int) $currentTimestamp;
    }

    public function getAllRecords(): array
    {
        $allKey = KeyValue::all();
        $response = [];

        foreach ($allKey->getIterator() as $keyValueObject) {
            $response += DataMapper::fromInstanceToDictionary($keyValueObject);
        }

        return $response;
    }

    /**
     * @param  array<string, mixed>  $dataBaseSavableObjects
     * @return array{literal-string&non-falsy-string, array<0|1|2|3, mixed>}
     */
    private function prepareInsertQuery(array $dataBaseSavableObjects, Carbon $currentTime): array
    {
        $rawQueryString = sprintf(
            'INSERT INTO `%s` (`%s`, `%s`, `%s`, `%s`) ',
            TableConstant::TABLE_NAME,
            KeyValueDataObjectInterface::KEY,
            KeyValueDataObjectInterface::VALUE,
            KeyValueDataObjectInterface::TIMESTAMP,
            KeyValueDataObjectInterface::METADATA,
        );
        $binding = [];
        $valuesArr = [];

        foreach ($dataBaseSavableObjects as $value) {
            $valuesArr[] = '(?, ?, ?, ?)';
            $binding = array_merge($binding, [$value['key'], $value['value'], $currentTime->format('Y-m-d H:i:s'), $value['metadata']]);
        }
        $rawQueryString .= ' VALUES '.implode(',', $valuesArr).' ON DUPLICATE KEY UPDATE `value` = VALUES(value), timestamp = VALUES(timestamp), metadata = VALUES(metadata)';

        return [$rawQueryString, $binding];
    }
}
