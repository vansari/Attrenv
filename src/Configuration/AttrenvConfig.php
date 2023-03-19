<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Configuration;

use DevCircleDe\Attrenv\Decorator\EnvParserInterface;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\EnvReader\EnvParser;
use DI\Container;
use DI\ContainerBuilder;

class AttrenvConfig
{
    public static function getContainer(?string $configPath = null): Container
    {
        $builder = new ContainerBuilder();
        if (class_exists(EnvParser::class)) {
            $builder->addDefinitions([
                EnvParserInterface::class => new EnvParser(),
            ]);
        }
        $builder->addDefinitions([
            ValueFactory::class => fn (Container $c): ValueFactory
                => new ValueFactory($c->get(EnvParserInterface::class))
        ]);
        $rootPath = dirname(__DIR__, 5);
        $configFile = $rootPath . DIRECTORY_SEPARATOR . '/config/attrenv-config.php';
        if (file_exists($configFile)) {
            $builder->addDefinitions($configFile);
        }
        if (null !== $configPath) {
            $builder->addDefinitions($configPath);
        }

        return $builder->build();
    }
}
