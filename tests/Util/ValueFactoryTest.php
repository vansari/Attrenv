<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\Util;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use DevCircleDe\Attrenv\ValueObject\Value;
use DevCircleDe\EnvReader\EnvParserInterface;
use DevCircleDe\EnvReader\Exception\ConvertionException;
use DevCircleDe\EnvReader\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DevCircleDe\Attrenv\Util\ValueFactory
 */
class ValueFactoryTest extends TestCase
{
    /**
     * @covers ::createValueFromMetaData
     */
    public function testCreateValueFromMetaDataWithReflNamedType(): void
    {
        $envParser = \Mockery::mock(EnvParserInterface::class);
        $envParser->shouldReceive('parse')->andReturn('secret');

        $attribute = new EnvironmentValue(envName: 'FOO_BAR');

        $reflAttribute = \Mockery::mock(\ReflectionAttribute::class);
        $reflAttribute->allows()->newInstance()->andReturn($attribute);

        $reflType = \Mockery::mock(\ReflectionNamedType::class);
        $reflType->allows()->allowsNull()->andReturn(true);
        $reflType->allows()->isBuiltin()->andReturn(true);
        $reflType->allows()->getName()->andReturn('string');

        $metaData = new MetaData('fooBar', $reflType, $reflAttribute);
        $valueFactory = new ValueFactory($envParser);
        $value = $valueFactory->createValueFromMetaData($metaData);

        $this->assertIsString($value->getValue());
        $this->assertTrue($value->isNullable());
        $this->assertSame('fooBar', $value->getName());
    }

    /**
     * @covers ::createValueFromMetaData
     */
    public function testCreateValueFromMetaDataWithReflUnionType(): void
    {
        $envParser = \Mockery::mock(EnvParserInterface::class);
        $envParser->shouldReceive('parse')->andReturn(123456);

        $attribute = new EnvironmentValue(envName: 'FOO_BAR');

        $reflAttribute = \Mockery::mock(\ReflectionAttribute::class);
        $reflAttribute->allows()->newInstance()->andReturn($attribute);

        $reflTypeString = \Mockery::mock(\ReflectionNamedType::class);
        $reflTypeString->allows()->isBuiltin()->andReturn(true);
        $reflTypeString->allows()->allowsNull()->andReturn(true);
        $reflTypeString->allows()->getName()->andReturn('string');

        $reflTypeInt = \Mockery::mock(\ReflectionNamedType::class);
        $reflTypeInt->allows()->isBuiltin()->andReturn(true);
        $reflTypeInt->allows()->allowsNull()->andReturn(true);
        $reflTypeInt->allows()->getName()->andReturn('int');

        $reflUnionType = \Mockery::mock(\ReflectionUnionType::class);
        $reflUnionType->allows()->getTypes()->andReturn([$reflTypeString, $reflTypeInt]);

        $metaData = new MetaData('fooBar', $reflUnionType, $reflAttribute);
        $valueFactory = new ValueFactory($envParser);
        $value = $valueFactory->createValueFromMetaData($metaData);

        $this->assertIsInt($value->getValue());
        $this->assertTrue($value->isNullable());
        $this->assertSame('fooBar', $value->getName());
    }

    /**
     * @covers ::createValueFromMetaData
     */
    public function testCreateValueFromMetaDataWithReflIntersectionTypeInUnionTypeWillReturnBuiltInType(): void
    {
        $envParser = \Mockery::mock(EnvParserInterface::class);
        $envParser->shouldReceive('parse')->andReturn(123456);

        $attribute = new EnvironmentValue(envName: 'FOO_BAR');

        $reflAttribute = \Mockery::mock(\ReflectionAttribute::class);
        $reflAttribute->allows()->newInstance()->andReturn($attribute);

        $reflIntersection = \Mockery::mock(\ReflectionIntersectionType::class);

        $reflTypeInt = \Mockery::mock(\ReflectionNamedType::class);
        $reflTypeInt->allows()->isBuiltin()->andReturn(true);
        $reflTypeInt->allows()->allowsNull()->andReturn(true);
        $reflTypeInt->allows()->getName()->andReturn('int');

        $reflUnionType = \Mockery::mock(\ReflectionUnionType::class);
        $reflUnionType->allows()->getTypes()->andReturn([$reflIntersection, $reflTypeInt]);

        $metaData = new MetaData('fooBar', $reflUnionType, $reflAttribute);
        $valueFactory = new ValueFactory($envParser);
        $value = $valueFactory->createValueFromMetaData($metaData);

        $this->assertIsInt($value->getValue());
        $this->assertTrue($value->isNullable());
        $this->assertSame('fooBar', $value->getName());
    }

    /**
     * @covers ::createValueFromMetaData
     */
    public function testCreateValueFromMetaDataReturnNullByConvertionException(): void
    {
        $envParser = \Mockery::mock(EnvParserInterface::class);
        $envParser->shouldReceive('parse')->andThrows(ConvertionException::class);

        $attribute = new EnvironmentValue(envName: 'FOO_BAR');

        $reflAttribute = \Mockery::mock(\ReflectionAttribute::class);
        $reflAttribute->allows()->newInstance()->andReturn($attribute);

        $reflTypeString = \Mockery::mock(\ReflectionNamedType::class);
        $reflTypeString->allows()->isBuiltin()->andReturn(true);
        $reflTypeString->allows()->allowsNull()->andReturn(true);
        $reflTypeString->allows()->getName()->andReturn('string');

        $metaData = new MetaData('fooBar', $reflTypeString, $reflAttribute);
        $valueFactory = new ValueFactory($envParser);
        $value = $valueFactory->createValueFromMetaData($metaData);

        $this->assertNull($value);
    }

    /**
     * @covers ::createValueFromMetaData
     */
    public function testCreateValueFromMetaDataReturnValueWithNullByNotFoundException(): void
    {
        $envParser = \Mockery::mock(EnvParserInterface::class);
        $envParser->shouldReceive('parse')->andThrows(NotFoundException::class);

        $attribute = new EnvironmentValue(envName: 'FOO_BAR');

        $reflAttribute = \Mockery::mock(\ReflectionAttribute::class);
        $reflAttribute->allows()->newInstance()->andReturn($attribute);

        $reflTypeString = \Mockery::mock(\ReflectionNamedType::class);
        $reflTypeString->allows()->isBuiltin()->andReturn(true);
        $reflTypeString->allows()->allowsNull()->andReturn(true);
        $reflTypeString->allows()->getName()->andReturn('string');

        $metaData = new MetaData('fooBar', $reflTypeString, $reflAttribute);
        $valueFactory = new ValueFactory($envParser);
        $value = $valueFactory->createValueFromMetaData($metaData);

        $this->assertNotNull($value);
        $this->assertInstanceOf(Value::class, $value);
        $this->assertSame('fooBar', $value->getName());
        $this->assertNull($value->getValue());
        $this->assertTrue($value->isNullable());
    }
}
