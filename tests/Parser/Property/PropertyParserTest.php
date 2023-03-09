<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\Parser\Property;

use DevCircleDe\Attrenv\Parser\Property\PropertyParser;
use DevCircleDe\Attrenv\Tests\data\TestClassWithProperties;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\PropertyFactory;
use DevCircleDe\EnvReader\EnvParser;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DevCircleDe\Attrenv\Parser\Property\PropertyParser
 */
class PropertyParserTest extends TestCase
{

    /**
     * @covers ::parse
     */
    public function testParseProperty(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new PropertyParser(EnvParser::getInstance(), new MetaDataFactory(), new PropertyFactory());
        /** @var TestClassWithProperties $testClassObject */
        $testClassObject = $parser->parse(TestClassWithProperties::class);
        $this->assertSame('fooBar', $testClassObject->getDatabaseName());
        $this->assertSame('s€cr€t', $testClassObject->getDatabasePassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertEquals(['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]], $testClassObject->getOptionsFromJson());
    }
}
