<?php

namespace TecnoSpeed\Plugnotas\Helpers;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\ReflectionHydrator;

/**
 * Classe singleton que atua como um wrapper para refatorar
 * a dependência do componente ferfabricio/hydrator.
 */
class Hydrator
{
  protected static array $hydrator = [];

  public static function hydrate(string $class, array $data): object
  {
    return static::getHydrator(ClassMethodsHydrator::class)->hydrate($data, new $class());
  }

  public static function filterNull(&$array)
  {
    if (is_array($array)) {
      foreach ($array as $a) {
        static::filterNull($a);
      }

      $array = array_filter($array, fn ($a) => $a !== null);
    }
  }

  /**
   * Extraí recursivamente um objeto.
   * Não é o ideal, mas é o que a aplicação espera.
   */
  public static function extract(object $object): array
  {
    $values = static::getHydrator(ReflectionHydrator::class)->extract($object);

    $values = array_map(function ($item) {
      if (is_object($item)) {
        return Hydrator::extract($item);
      }

      if (is_array($item)) {
        $new_item = [];
        foreach ($item as $i) {
          if (is_object($i)) {
            $new_item[] = Hydrator::extract($i);
          } else {
            $new_item[] = $i;
          }
        }
        return $new_item;
      }

      return $item;
    }, $values);

    static::filterNull($values);

    return $values;
  }

  protected static function getHydrator(string $strategy): HydratorInterface
  {
    if (isset(static::$hydrator[$strategy]) === false) {
      static::$hydrator[$strategy] = new $strategy();
    }

    return static::$hydrator[$strategy];
  }
}
