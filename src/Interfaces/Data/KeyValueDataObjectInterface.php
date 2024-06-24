<?php

declare(strict_types=1);

namespace Minhducck\KeyValueDataStorage\Interfaces\Data;

use Minhducck\KeyValueDataStorage\Models\KeyValue;

interface KeyValueDataObjectInterface
{
    const KEY = 'key';
    const VALUE = 'value';
    const TIMESTAMP = 'timestamp';
    const METADATA = 'metadata';

    /**
     * @param string $key
     * @return KeyValueDataObjectInterface
     */
    public function setObjectKey(string $key): KeyValueDataObjectInterface;

    /**
     * @return string
     */
    public function getObjectKey(): string;

    /**
     * @param mixed $value
     * @return KeyValueDataObjectInterface
     */
    public function setValue(mixed $value): KeyValueDataObjectInterface;

    /**
     * @return mixed
     */
    public function getValue(): mixed;


    /**
     * @param int $timestamp
     * @return KeyValueDataObjectInterface
     */
    public function setTimestamp(int $timestamp): KeyValueDataObjectInterface;

    /**
     * @return int
     */
    public function getTimestamp(): int;

    /**
     * @return null|mixed
     */
    public function getMetadata(string $key = ''): mixed;

    /**
     * @param string $key
     * @param mixed  $value
     * @return KeyValueDataObjectInterface
     */
    public function setMetadata(string $key, mixed $value): KeyValueDataObjectInterface;

    /**
     * @param string $key
     * @param mixed $value
     * @return KeyValueDataObjectInterface
     */
    public static function createKeyValueDataObject(
        string $key,
        mixed $value
    ): KeyValueDataObjectInterface;

    /**
     * @return array<string, mixed>
     */
    public function serialize(): array;
}
