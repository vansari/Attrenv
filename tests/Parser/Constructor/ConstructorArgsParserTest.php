<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\Parser\Constructor;

use DevCircleDe\Attrenv\Parser\Constructor\ConstructorArgsParser;
use DevCircleDe\Attrenv\Tests\data\TestClassWithAttributeInConstructor;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\PropertyFactory;
use DevCircleDe\EnvReader\EnvParser;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \DevCircleDe\Attrenv\Parser\Constructor\ConstructorArgsParser
 */
class ConstructorArgsParserTest extends TestCase
{

    /**
     * @covers ::parse
     */
    public function testParse(): void
    {
        putenv('DATABASE_NAME=fooBar');
        putenv('DATABASE_PASSWORD=s€cr€t');
        putenv('DATABASE_PORT=1234');
        putenv("DB_OPTION_JSON={\"name\":\"secretName\", \"values\":[{\"value1\":123},{\"value2\":\"baz\"}]}");

        $parser = new ConstructorArgsParser(EnvParser::getInstance(), new MetaDataFactory(), new PropertyFactory());
        /** @var TestClassWithAttributeInConstructor $testClassObject */
        $testClassObject = $parser->parse(TestClassWithAttributeInConstructor::class);
        dump($testClassObject);
        $this->assertSame('fooBar', $testClassObject->getDatabaseName());
        $this->assertSame('s€cr€t', $testClassObject->getDatabasePassword());
        $this->assertSame(1234, $testClassObject->getDatabasePort());
        $this->assertEquals(['name' => 'secretName', 'values' => [['value1' => 123], ['value2' => 'baz']]], $testClassObject->getOptionsFromJson());
    }
}
