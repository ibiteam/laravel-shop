<?php

namespace App\Components;

abstract class PageComponent implements ComponentImp
{
    /**
     * @var mixed
     */
    protected string $name;
    private string $component_name;
    private string $alias;

    /**
     * @param mixed|string $name
     */
    public function __construct(string $component_name = '', string $name = '')
    {
        $this->name = $name;
        $this->component_name = $component_name;
        $this->alias = $component_name;
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static())->$method(...$parameters);
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getComponentName(): string
    {
        return $this->component_name;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
