<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2022 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Boot\System\Queue;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Queue\Attributes\WithoutRelations;
use Illuminate\Queue\SerializesAndRestoresModelIdentifiers;

trait SerializesModels
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * Подготовьте значения экземпляра для сериализации.
     *
     * @return array
     */
    public function __serialize()
    {
        $values = [];

        $reflectionClass = new ReflectionClass($this);

        [$class, $properties, $classLevelWithoutRelations] = [
            get_class($this),
            $reflectionClass->getProperties(),
            ! empty($reflectionClass->getAttributes(WithoutRelations::class)),
        ];

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            if (! $property->isInitialized($this)) {
                continue;
            }

            $value = $this->getPropertyValue($property);

            if ($property->hasDefaultValue() && $value === $property->getDefaultValue()) {
                continue;
            }

            $name = $property->getName();

            if ($property->isPrivate()) {
                $name = "\0{$class}\0{$name}";
            } elseif ($property->isProtected()) {
                $name = "\0*\0{$name}";
            }

            $values[$name] = $this->getSerializedPropertyValue(
                $value,
                ! $classLevelWithoutRelations &&
                    empty($property->getAttributes(WithoutRelations::class))
            );
        }

        return $values;
    }

    /**
     * Восстановите модель после сериализации.
     *
     * @param  array  $values
     * @return void
     */
    public function __unserialize(array $values)
    {
        $properties = (new ReflectionClass($this))->getProperties();

        $class = get_class($this);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $name = $property->getName();

            if ($property->isPrivate()) {
                $name = "\0{$class}\0{$name}";
            } elseif ($property->isProtected()) {
                $name = "\0*\0{$name}";
            }

            if (! array_key_exists($name, $values)) {
                continue;
            }

            $property->setValue(
                $this, $this->getRestoredPropertyValue($values[$name])
            );
        }
    }

    /**
     * Получите значение свойства для данного свойства.
     *
     * @param  \ReflectionProperty  $property
     * @return mixed
     */
    protected function getPropertyValue(ReflectionProperty $property)
    {
        return $property->getValue($this);
    }
}
