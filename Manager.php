<?php
namespace SMGregsList;
class Manager
{
    protected $code;
    protected $name;

    function __set($name, $value)
    {
        if ($name == 'id') {
            $this->id = $value;
        }
        if ($name == 'code') {
            $this->code = $value;
        }
        if ($name == 'name') {
            $this->name = $value;
        }
        throw new \Exception('Internal error: unknown manager variable: ' . $name);
    }

    function getName()
    {
        return $this->name;
    }

    function getCode()
    {
        return $this->code;
    }

    function generateCode()
    {
        $this->code = dechex($this->id) . '-' . bin2hex(openssl_random_pseudo_bytes(5));
    }
}