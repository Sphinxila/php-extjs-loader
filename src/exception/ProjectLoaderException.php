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

namespace ExtJSLoader\Exception;

class ProjectLoaderException extends \Exception {
    /** @var string */
    private $path;

    /**
     * ProjectLoaderExceptions constructor.
     * @param string $path
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(string $path, string $message, int $code = 0, \Exception $previous = null)
    {
        $this->path = $path;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Path
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
