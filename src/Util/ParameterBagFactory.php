<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Util;

use DevCircleDe\Attrenv\ValueObject\ParameterValue;
use DevCircleDe\Attrenv\ValueObject\ParameterValueBag;
use DevCircleDe\Attrenv\ValueObject\Value;
use ReflectionParameter;

class ParameterBagFactory
{
    /**
     * @param ReflectionParameter[] $parameters
     * @psalm-param array<array-key, Value|null> $values
     * @return ParameterValueBag
     */
    public function create(array $parameters, array $values): ParameterValueBag
    {
        $parameterBag = new ParameterValueBag();
        foreach ($parameters as $index => $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $defaultValue = $parameter->getDefaultValue();
            }
            $parameterBag->addParameterValueAtIndex(
                new ParameterValue(
                    $index,
                    $parameter->getName(),
                    $parameter->allowsNull(),
                    $parameter->isDefaultValueAvailable(),
                    $defaultValue ?? null
                ),
                $index
            );
        }

        return $this->setParameterValues($values, $parameterBag);
    }

    /**
     * @param array $parsedValues
     * @param ParameterValueBag $parameterBag
     * @return ParameterValueBag
     */
    private function setParameterValues(array $parsedValues, ParameterValueBag $parameterBag): ParameterValueBag
    {
        foreach ($parsedValues as $index => $value) {
            $parameterValue = $parameterBag->getParameterValueAtIndex($index);
            if (null === $value) {
                if (!$parameterValue->hasDefaultValue()) {
                    if ($parameterValue->isNullable()) {
                        $parameterValue->setValue(
                            new Value(
                                $parameterValue->getName(),
                                null,
                                $parameterValue->isNullable()
                            )
                        );
                        continue;
                    }
                    throw new \LogicException(
                        "No value was set via EnvironmentValue. Constructor Parameter at index $index has "
                        . "no default value and is not nullable."
                    );
                }
                continue;
            }
            $parameterValue->setValue($value);
        }

        return $parameterBag;
    }
}
