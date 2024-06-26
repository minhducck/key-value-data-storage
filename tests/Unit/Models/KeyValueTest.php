<?php

namespace Minhducck\KeyValueDataStorage\Test\Unit\Models;

use Minhducck\KeyValueDataStorage\Helpers\DataTypeResolver;
use Minhducck\KeyValueDataStorage\Interfaces\Data\KeyValueDataObjectInterface;
use Minhducck\KeyValueDataStorage\Models\KeyValue;
use Orchestra\Testbench\TestCase;

class KeyValueTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testCreateKeyValueObject()
    {
        $obj = KeyValue::createKeyValueDataObject('key', 'value');
        $this->assertInstanceOf(KeyValueDataObjectInterface::class, $obj);
        $this->assertIsArray($obj->getMetadata());
        $this->assertEquals('key', $obj->getObjectKey());
        $this->assertEquals('value', $obj->getValue());

        $this->assertArrayHasKey(KeyValue::METADATA_DATA_TYPE, $obj->getMetadata());
        $this->assertEquals(
            DataTypeResolver::TYPE_STRING,
            $obj->getMetadata(KeyValue::METADATA_DATA_TYPE)
        );
    }

    public function testObjectMetadata()
    {
        $keyValueObject = KeyValue::createKeyValueDataObject('sample', 0b101);
        $sampleMetadataKey = 'expirationTime';
        $expTime = 10000123;
        $keyValueObject->setMetadata($sampleMetadataKey, $expTime);

        $this->assertIsArray($keyValueObject->getMetadata());
        $this->assertArrayHasKey($sampleMetadataKey, $keyValueObject->getMetadata());
        $this->assertArrayHasKey(KeyValue::METADATA_DATA_TYPE, $keyValueObject->getMetadata());
        $this->assertEquals($expTime, $keyValueObject->getMetadata($sampleMetadataKey));
    }

    public function testObjectResolveDatatypeItself()
    {
        $keyValueObject = KeyValue::createKeyValueDataObject('sample', true);
        $this->assertEquals(DataTypeResolver::TYPE_BOOL, $keyValueObject->getDataType());
    }
}
