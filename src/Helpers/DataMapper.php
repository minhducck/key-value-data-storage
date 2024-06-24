<?php
declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Helpers;

use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;
use Minhducck\KeyValueDataStorage\Models\KeyValue;

class DataMapper
{
    /**
     * @param array<string, mixed> $dictionary
     * @return KeyValueDataObjectInterface[]
     */
    public static function dictionaryToKeyValueInstances(array $dictionary, int $timestamp): array
    {
        $instances = [];
        foreach ($dictionary as $key => $value) {
            $instance = KeyValue::createKeyValueDataObject($key, $value);
            $instance->setTimestamp($timestamp);
            $instances[] = $instance;
        }
        return $instances;
    }

    /**
     * @param KeyValue $instance
     * @return array<string, mixed>
     */
    public static function fromInstanceToDictionary(KeyValueDataObjectInterface $instance): array
    {
        $key = $instance->getObjectKey();
        $value = DataTypeResolver::castData($instance->getValue(), $instance->getDataType());

        return [
            $key => $value,
        ];
    }
}
