# Attrenv - Extension for DevCircleDe/EnvReader

With the Attrenv you can declare classes to set properties or constructor args with Env Variable values.

It is important that you have installed the DevCircleDe/EnvReader.

The Parser uses the property name as the ENV Name. But you can also pass a custom ENV Name to the Attribute.
Each Parameter/Property must be Type hinted, or you must declare the Type in the EnvironmentValue Attribute. 

The EnvironmentValue only supports Types which the [EnvReader](https://github.com/devcircle-de/EnvReader) supports.

If you have created your classes you can automatically instanciate your class with the AttributeParser.

Usage PropertyParser:
```injectablephp
<?php

declare(strict_types=1);

namespace Your\Namespace;

use DevCircleDe\Attrenv\Attribute\AttributeEnvParser;
use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Parser\Property\PropertyParser;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;

#[AttributeEnvParser(new PropertyParser(new MetaDataFactory(), new ValueFactory()))]
class TestClassWithProperties
{
    #[EnvironmentValue]
    private ?string $databaseName = null;

    #[EnvironmentValue]
    private ?string $databasePassword = null;

    #[EnvironmentValue]
    private ?int $databasePort = null;

    private array $options = [];

    #[EnvironmentValue('json', 'DB_OPTION_JSON')]
    private array $optionsFromJson = [];
}
```
Usage ConstructorArgs:

```injectablephp
<?php

declare(strict_types=1);

namespace Your\Namespace;

use DevCircleDe\Attrenv\Attribute\AttributeEnvParser;
use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Parser\Constructor\ConstructorArgsParser;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;

#[AttributeEnvParser(new ConstructorArgsParser(new MetaDataFactory(), new ValueFactory()))]
class TestClassWithAttributeInConstructor
{
    public function __construct(
        #[EnvironmentValue]
        private readonly string $databaseName,
        #[EnvironmentValue]
        private readonly string $databasePassword,
        #[EnvironmentValue]
        private readonly int $databasePort,
        private readonly array $options = [],
        #[EnvironmentValue('json', 'DB_OPTION_JSON')]
        private readonly array $optionsFromJson = [],
    ) {
    }
}
```

Usage AttributeParser:
```injectablephp
<?php

use DevCircleDe\Attrenv\Attribute\AttributeEnvParser;
use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Parser\AttributeParser;
use DevCircleDe\Attrenv\Parser\Property\PropertyParser;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;

#[AttributeEnvParser(
    new PropertyParser(
        new MetaDataFactory(),
        new ValueFactory()
    )
)]
class EnvConfiguredClass {

    #[EnvironmentValue]
    private string $secret;
    
    #[EnvironmentValue(envName: 'ENV_NAME')]
    private string $betterInternalName;
    
    #[EnvironmentValue(type: 'json')]
    private array $configArrayJson;
    
}

$envConfiguredClass = (new AttributeParser())->parse(EnvConfiguredClass::class)
```