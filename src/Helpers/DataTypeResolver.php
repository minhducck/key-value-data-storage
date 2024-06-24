<?php
declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Helpers;

class DataTypeResolver
{
    const TYPE_NULL = 'NULL';
    const TYPE_NUMBER = 'NUMBER';
    const TYPE_STRING = 'STRING';
    const TYPE_BOOL = 'BOOL';
    const TYPE_ARRAY = 'ARRAY';

    /**
     * @param mixed $value
     * @return string
     */
    static function resolve(mixed $value): string
    {
        if ($value === null) {
            return self::TYPE_NULL;
        }

        if (is_array($value)) {
            return self::TYPE_ARRAY;
        }

        if (is_bool($value)) {
            return self::TYPE_BOOL;
        }

        if (is_numeric($value)) {
            return self::TYPE_NUMBER;
        }

        return self::TYPE_STRING;
    }

    static function castData(mixed $value, string $dataType): mixed
    {
        try {
            return match ($dataType) {
                self::TYPE_NULL => null,
                self::TYPE_BOOL => (bool)$value,
                self::TYPE_NUMBER => $value + 0,
                self::TYPE_ARRAY => json_decode($value, true, flags: JSON_THROW_ON_ERROR),
                self::TYPE_STRING => (string)$value,
                default => throw new \RuntimeException('Unknown datatype.'),
            };
        } catch (\JsonException | \ErrorException | \TypeError $exception) {
            throw new \RuntimeException('Corrupted data.');
        }
    }

    static function serializeValue(mixed $value): mixed
    {
        $dataType = self::resolve($value);
        if ($dataType === self::TYPE_ARRAY) {
            return json_encode($value);
        }

        return self::castData($value, $dataType);
    }
}
