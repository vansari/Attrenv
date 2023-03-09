<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\Parser;

use DevCircleDe\Attrenv\Parser\AttributeParser;
use DevCircleDe\Attrenv\Tests\data\TestClassWithAttributeInConstructor;
use DevCircleDe\Attrenv\Tests\data\TestClassWithProperties;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DevCircleDe\Attrenv\Parser\AttributeParser
 */
class AttributeParserTest extends TestCase
{
    /**
     * @covers ::parse
     */
    public function testParseWithConstructorArgs(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new AttributeParser();
        $testClassObject = $parser->parse(TestClassWithAttributeInConstructor::class);
        $this->assertSame('fooBar', $testClassObject->getDatabaseName());
        $this->assertSame('s€cr€t', $testClassObject->getDatabasePassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertSame([], $testClassObject->getOptions());
        $this->assertEquals(
            ['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]],
            $testClassObject->getOptionsFromJson()
        );
    }

    /**
     * @covers ::parse
     */
    public function testParseWithProperty(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new AttributeParser();
        $testClassObject = $parser->parse(TestClassWithProperties::class);
        $this->assertSame('fooBar', $testClassObject->getDatabaseName());
        $this->assertSame('s€cr€t', $testClassObject->getDatabasePassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertSame([], $testClassObject->getOptions());
        $this->assertEquals(
            ['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]],
            $testClassObject->getOptionsFromJson()
        );
    }
}
