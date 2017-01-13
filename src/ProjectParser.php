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

namespace ExtJSLoader;

use ExtJSLoader\Facade\Parser;

class ProjectParser {
    /** @var Parser[] */
    private static $parser = [];

    /**
     * Register a new parser
     * @param Parser $parser
     */
    public static function registerParser(Parser $parser) {
        self::$parser[$parser->getParserID()] = $parser;
    }

    /**
     * Get parser
     * @return \ExtJsLoader\Facade\Parser[]
     */
    public static function getParser()
    {
        return self::$parser;
    }

    /**
     * Loop trough all registered parsers
     * @param string $path
     * @param string $content
     * @return string
     */
    public static function parse(string $path, string &$content)
    {
        foreach (self::$parser as $parser) {
            $content = $parser->parse($path, $content);
        }
    }
}