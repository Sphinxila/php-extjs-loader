<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Test project
 * Class TestProject
 */
class TestCompressedProject {
    /** @var \ExtJSLoader\Model\Project */
    private $loader;

    /**
     * Test project
     * TestProject constructor.
     */
    public function __construct()
    {
        $this->loader = new \ExtJSLoader\Project(
            "TestArchitectProject",
            __DIR__ . "/../test/TestArchitectProject",
            __DIR__ . "/TestCompressedProject.xvt",
            "test-destination"
        );

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

// Test project
$test = new TestCompressedProject();

// Code
/**
 * Write code output into html including extJS files
 */
$code = $test->getLoader()->getCode(true);
