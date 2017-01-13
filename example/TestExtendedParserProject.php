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

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Test project
 * Class TestProject
 */
class TestExtendedParserProject {
    /** @var \ExtJSLoader\Model\Project */
    private $loader;

    /**
     * Test project
     * TestProject constructor.
     */
    public function __construct()
    {
        // ExtJS Loader
        $this->loader = new \ExtJSLoader\Project(
            "TestArchitectProject",
            __DIR__ . "/../test/TestArchitectProject",
            __DIR__ . "/TestExtendedParserProject.xvt",
            "test-destination"
        );

        // Use compiled project if exists
        $this->loader->load();
    }

    /**
     * Loader
     * @return \ExtJSLoader\Model\Project|\ExtJSLoader\Project
     */
    public function getLoader()
    {
        return $this->loader;
    }
}

/**
 * Test parser
 * Class Parser
 */
class Parser implements \ExtJSLoader\Facade\Parser {
    /**
     * Parser
     * @param string $path
     * @param string $str
     * @return string
     */
    public function parse(string $path, string $str): string
    {
        // Replace content here like static variables {{STATIC_TEST}}
        echo "Replacing content in: ".$path . "\n";
        return $str;
    }

    /**
     * Unique parser ID
     * @return string
     */
    public function getParserID(): string
    {
        return "staticParser";
    }
}

// Register parser
\ExtJSLoader\ProjectParser::registerParser((new Parser()));

// Test project
$test = new TestExtendedParserProject();

// Code
/**
 * Write code output into html including extJS files
 */
$code = $test->getLoader()->getCode();