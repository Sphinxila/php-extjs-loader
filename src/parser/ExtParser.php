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

namespace ExtJSLoader\Parser;

trait ExtParser {
    /**
     * String between
     * @param $string
     * @param $start
     * @param $end
     * @return string
     */
    private function getStringBetween($string, $start, $end): string {
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
     * @return string
     */
    public function parseAppJS(string &$buffer, array &$info)
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
                $launch .= "var application = new ".$matches[1]."(); \n";
                $launch = str_replace($matches[0], "", $launch . $matches[0]);
            }
        }

        // Trim
        $code = trim($this->getStringBetween($buffer, "launch: function() {", "}"));
        $buffer = str_replace($code, $launch, $buffer);
    }
}