<?php

namespace ExtJSLoader\Facade;

interface Parser {
    /**
     * This function needs to return the parsed string
     * @param string $path
     * @param string $str
     * @return string
     */
    public function parse(string $path, string $str): string;


    /**
     * This functions provides the internal parser ID or Name that will be used to register the parser
     * - Need to be unique
     * @return string
     */
    public function getParserID(): string;
}
