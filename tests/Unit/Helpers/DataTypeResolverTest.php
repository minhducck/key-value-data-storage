<?php

namespace Minhducck\KeyValueDataStorage\Test\Unit\Helpers;

use Orchestra\Testbench\TestCase;
use Minhducck\KeyValueDataStorage\Helpers\DataTypeResolver;

class DataTypeResolverTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function testResolve()
    {
        $this->assertEquals(DataTypeResolver::TYPE_NULL, DataTypeResolver::resolve(null));

        $this->assertEquals(DataTypeResolver::TYPE_ARRAY, DataTypeResolver::resolve(['a' => ['b' => 'c']]));
        $this->assertEquals(DataTypeResolver::TYPE_ARRAY, DataTypeResolver::resolve([1, 2, 3, 4]));

        $this->assertEquals(DataTypeResolver::TYPE_BOOL, DataTypeResolver::resolve(true));
        $this->assertEquals(DataTypeResolver::TYPE_BOOL, DataTypeResolver::resolve(false));

        $this->assertEquals(DataTypeResolver::TYPE_NUMBER, DataTypeResolver::resolve(0));
        $this->assertEquals(DataTypeResolver::TYPE_NUMBER, DataTypeResolver::resolve(-1));
        $this->assertEquals(DataTypeResolver::TYPE_NUMBER, DataTypeResolver::resolve(0xDEADBEEFCAFFEBABE));
        $this->assertEquals(DataTypeResolver::TYPE_NUMBER, DataTypeResolver::resolve(0b100001));
        $this->assertEquals(DataTypeResolver::TYPE_NUMBER, DataTypeResolver::resolve(10_123.456));

        $this->assertEquals(DataTypeResolver::TYPE_STRING, DataTypeResolver::resolve("Foo"));
    }

    public function testCastDataOnDataTypeMismatch()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unknown datatype.');
        DataTypeResolver::castData(["Hello"], 'NOT_MATCH_ANY_TYPE');
    }


    public function testCastDataOnCorruptedData()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Corrupted data.');

        DataTypeResolver::castData("THIS IS STRING", DataTypeResolver::TYPE_NUMBER);
    }

    public function testCastDataOnArrayStringConversion()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Corrupted data.');

        DataTypeResolver::castData(["Hello"], DataTypeResolver::TYPE_STRING);
    }

    public function testCastDataOnInvalidJsonContent()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Corrupted data.');

        DataTypeResolver::castData('', DataTypeResolver::TYPE_ARRAY);
    }

    public function testSerializeValue()
    {
        $this->assertJson(DataTypeResolver::serializeValue(['foo' => 'bar']));
        $this->assertNull(DataTypeResolver::serializeValue(null));
        $this->assertEquals('Simple String', DataTypeResolver::serializeValue('Simple String'));
    }
}
