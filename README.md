![workflow](https://github.com/devcircle-de/Attrenv/actions/workflows/php.yml/badge.svg)

# Attrenv - Extension for DevCircleDe/EnvReader

With the Attrenv you can instantiate classes with Env Variable values.

### Installation

```shell
composer require devcirclede/attrenv
```

(It is recommended that you have installed the [EnvReader](https://github.com/devcircle-de/EnvReader).)

### Description
The Parser uses the property name as the ENV Name. But you can also pass a custom ENV Name to the Attribute.
Each Parameter/Property must be Type hinted, or you must declare the Type in the EnvironmentValue Attribute. 

The EnvironmentValue only supports Types which the [EnvReader](https://github.com/devcircle-de/EnvReader) supports.

If you have created your classes you can automatically load the Values from Env and instantiate your class with the AttributeParser.

#### USAGE

Usage Attrenv:
```php
<?php

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Attrenv;

class EnvConfiguredClass {

    #[EnvironmentValue]
    private string $secret;
    
    #[EnvironmentValue(envName: 'ENV_NAME')]
    private string $betterInternalName;
    
    #[EnvironmentValue(type: 'json', default: ['simple' => 'better'])]
    private array $configArrayJson;
    
    public function __construct(
        #[EnvironmentValue]
        private readonly string $databaseName,
        #[\SensitiveParameter]
        #[EnvironmentValue]
        string $secret,
        private readonly array $optional = []
    ) {
    
    }
    
}

$envConfiguredClass = (new Attrenv())->getParser()->parse(EnvConfiguredClass::class);
```

You have also the possibility to use your own EnvParser Implementation.

Create your own Parser and implement the DevCircleDe\Attrenv\Decorator\EnvParserInterface.

The Attrenv uses the php-di package for dependency injection.

For autoload the Config, create a config file DOCUMENT_ROOT/config/attrenv-config.php:

```php
<?php

return [
    'DevCircleDe\Attrenv\Decorator\EnvParserInterface' => new CustomParser(),
];

```

If you want to load it manual, create your file anywhere you want and load it:

```php

$envConfiguredClass = (new Attrenv('your/path/to/config.php'))->getParser()->parse(EnvConfiguredClass::class);
```