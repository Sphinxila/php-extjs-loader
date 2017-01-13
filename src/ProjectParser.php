<?php

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