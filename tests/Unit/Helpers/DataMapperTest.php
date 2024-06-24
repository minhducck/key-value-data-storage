<?php

namespace Minhducck\KeyValueDataStorage\Test\Unit\Helpers;

use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;
use Minhducck\KeyValueDataStorage\Models\KeyValue;
use Orchestra\Testbench\TestCase;
use Minhducck\KeyValueDataStorage\Helpers\DataMapper;

class DataMapperTest extends TestCase
{
    public function testDictionaryToKeyValueInstancesOnEmptyArray()
    {
        $timestamp = \Carbon\Carbon::now()->timestamp;
        $this->assertEmpty(DataMapper::dictionaryToKeyValueInstances([], $timestamp));
    }

    public function testDictionaryToKeyValueInstances()
    {
        $testInput = [
            'bool' => true,
            'boolFalse' => false,
            'array' => [0,1,1,3, "a"],
            'nullish' => null,
            'numbers' => 3.1415,
        ];

        $timestamp = \Carbon\Carbon::now()->timestamp;
        $instances = DataMapper::dictionaryToKeyValueInstances($testInput, $timestamp);
        $this->assertNotEmpty($instances);
        $this->assertIsArray($instances);
        $this->assertInstanceOf(KeyValueDataObjectInterface::class, $instances[0]);
        $this->assertEquals(array_map(fn ($instance) => $instance->getObjectKey(), $instances), array_keys($testInput));
    }

    public function testFromInstanceToDictionary ()
    {
        $inputObjectKey = 'bool';
        $keyValueInstance = KeyValue::createKeyValueDataObject($inputObjectKey, true);
        $dictionary = DataMapper::fromInstanceToDictionary($keyValueInstance);


        $this->assertNotEmpty($dictionary);
        $this->assertIsArray($dictionary);
        $this->assertArrayHasKey($inputObjectKey, $dictionary, 'Key is not equal after convert to dictionary.');
        $this->assertIsBool($dictionary[$inputObjectKey], "Value type mismatch");
        $this->assertTrue($dictionary[$inputObjectKey], "Value mismatch");
    }
}