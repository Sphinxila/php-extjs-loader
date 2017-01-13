<?php

namespace ExtJSLoader\Parser;

trait ExtParser {
    /**
     * String between
     * @param $string
     * @param $start
     * @param $end
     * @return string
     */
    private function getStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    /**
     * @param string $buffer
     * @param array $info
     */
    public function parseAppJS(string &$buffer, array $info)
    {
        // App instance
        $launch = $this->appName . ".appInstance = this;\n";

        // Get create part
        $pattern = '/Ext\.create\(\'(.*)\'\);/isU';

        // Preg match
        if (preg_match($pattern, $info["content"], $matches)) {
            // If new target is needed
            if (!is_null($this->target)) {
                $launch .= "Ext.getCmp('".$this->target."').add(new ".$matches[1]."()); \n";
                $launch = str_replace($matches[0], "", $launch);
            } else {
                $launch = str_replace($matches[0], "", $launch . $matches[0]);
            }
        }

        // Trim
        $code = trim($this->getStringBetween($buffer, "launch: function() {", "}"));
        $buffer = str_replace($code, $launch, $buffer);
    }
}