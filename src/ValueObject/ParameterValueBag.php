<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\ValueObject;

class ParameterValueBag
{
    private array $parameters = [];

    public function addParameterValueAtIndex(ParameterValue $value, int $index): void
    {
        if (array_key_exists($index, $this->parameters)) {
            throw new \InvalidArgumentException('Parameter already exists at Index ' . $index);
        }

        $this->parameters[$index] = $value;
    }

    public function getParameterValueAtIndex(int $index): ParameterValue
    {
        if (!array_key_exists($index, $this->parameters)) {
            throw new \InvalidArgumentException('No Value at Index ' . $index);
        }

        return $this->parameters[$index];
    }

    /**
     * @return ParameterValue[]
     */
    public function getParameterValues(): array
    {
        return $this->parameters;
    }

    public function fetchValues(): array
    {
        $values = [];
        foreach ($this->getParameterValues() as $parameterValue) {
            if (null === $parameterValue->getValue()) {
                if ($parameterValue->hasDefaultValue()) {
                    $values[$parameterValue->getIndex()] = $parameterValue->getDefaultValue();
                } elseif ($parameterValue->isNullable()) {
                    $values[$parameterValue->getIndex()] = null;
                }
                continue;
            }
            $values[$parameterValue->getIndex()] = $parameterValue->getValue()->getValue();
        }

        return $values;
    }
}
