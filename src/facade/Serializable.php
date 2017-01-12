<?php

namespace ExtJSLoader\Facade;

use ExtJsLoader\Exception\SerializeException;

trait Serializable {
    /**
     * Serializable
     * @var array
     */
    public $serializable = [];

    /** @var int */
    public $version = 0;

    /** @var string */
    private $versionKey = "ObjectSerializeVersion";

    /**
     * @param array $array
     * @return bool
     * @throws SerializeException
     */
    public function fromArray(array $array): bool
    {
        // Check if version defined
        if (!array_key_exists($this->versionKey, $array))
            throw new SerializeException($this->versionKey, "Failed to load version key from serialized object");

        // Invalid version
        if ($array[$this->versionKey] != $this->version)
            throw new SerializeException(
                $this->versionKey, "Version missmatch".$this->version." != ".$array[$this->versionKey]
            );

        // Load serialized
        foreach($this->serializable as $key) {
            if (!array_key_exists($array, $key))
                return false;
            $this->$key = $array[$key];
        }
        return true;
    }

    /**
     * To array
     * @return array
     */
    public function toArray(): array
    {
        $return = [];
        foreach($this->serializable as $key) {
            $return[$key] = $this->$key;
        }
        return $return;
    }

    /**
     * Sleep
     * @return array
     */
    public function __sleep()
    {
        return $this->serializable;
    }
}