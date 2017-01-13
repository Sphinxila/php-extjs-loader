<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Test project
 * Class TestProject
 */
class TestCompiledProject {
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
            __DIR__ . "/TestCompiledProject.xvt",
            "test-destination"
        );

        // Use compiled project if exists
        $this->loader->load(true, true);
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

// Test project
$test = new TestCompiledProject();

// Code
/**
 * Write code output into html including extJS files
 */
$code = $test->getLoader()->getCode();