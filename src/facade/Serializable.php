<?php

/**
 *  This file is part of the PHP-ExtJS Loader Project.
 *
 *  PHP-ExtJS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  PHP-ExtJS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with PHP-ExtJS.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @author: Sphinxila
 *  @date: 13.01.2017
 */

namespace ExtJSLoader\Facade;

use ExtJSLoader\Exception\SerializeException;

trait Serializable {
    /**
     * Serializable
     * @var array
     */
    //public $serializable = [];

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