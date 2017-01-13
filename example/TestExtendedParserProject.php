<?php

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