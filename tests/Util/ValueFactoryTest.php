<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\Util;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use DevCircleDe\EnvReader\EnvParser;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DevCircleDe\Attrenv\Util\ValueFactory
 */
class ValueFactoryTest extends TestCase
{
    /**
     * @covers ::createValueFromMetaData
     */
    public function testCreateValueFromMetaData(): void
    {
        $this->markTestIncomplete();
        $envParser = \Mockery::mock(EnvParser::getInstance())->makePartial();
        $envParser->shouldReceive('parse')->andReturn('secret');

        $attribute = new EnvironmentValue(envName: 'FOO_BAR');

        $reflAttribute = \Mockery::mock(\ReflectionAttribute::class);
        $reflAttribute->allows()->newInstance()->andReturn($attribute);

        $reflType = \Mockery::mock(\ReflectionNamedType::class);
        $reflType->allows()->allowsNull()->andReturn(true);

        $metaData = new MetaData('fooBar', $reflType, $reflAttribute);
        $valueFactory = new ValueFactory($envParser);
        $value = $valueFactory->createValueFromMetaData($metaData);

        $this->assertIsString($value->getValue());
        $this->assertTrue($value->isNullable());
        $this->assertSame('fooBar', $value->getName());
    }
}
