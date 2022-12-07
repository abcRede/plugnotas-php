<?php

namespace TecnoSpeed\Plugnotas\Abstracts;

use TecnoSpeed\Plugnotas\Helpers\Hydrator;
use TecnoSpeed\Plugnotas\Error\InvalidTypeError;
use TecnoSpeed\Plugnotas\Interfaces\IBuilder;

abstract class BuilderAbstract implements IBuilder
{
    public function toArray($excludeNull = true)
    {
        $values = Hydrator::extract($this);

        if ($excludeNull) {
            $values = array_filter($values, fn ($item) => $item !== null);
        }

        return $values;
    }

    public static function fromArray($data)
    {
        if (is_array($data)) {
            return Hydrator::hydrate(\get_called_class(), $data);
        }

        if (is_object($data) && get_class($data) === \get_called_class()) {
            return $data;
        }

        throw new InvalidTypeError('Deve ser informado um array.');
    }
}
