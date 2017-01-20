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
     * Launch code
     * @param string $code
     * @param string $appName
     * @param string $default
     * @return string
     */
    private function launchCode(string $code, string $appName, string $default): string
    {
        if (!is_null($code))
            return str_replace("{{loader_appName}}", $appName, $code);
        return $default;
    }

    /**
     * @param string $buffer
     * @param array $info
     * @return string
     */
    public function parseAppJS(string &$buffer, array &$info)
    {
        // App instance
        $launch = $this->launchCode(
            $this->launchCode,
            $this->appName,
            $this->appName . ".applicationInstance = this;"
        )."\n";

        // Patterns
        $patterns = [
            "default" => [
                "pattern" => "/Ext\.create\(\'(.*)\'\);/isU",
                "replacer" => function(string $launch, string &$buffer, array $matches, string $target = null): bool {
                    // Replace launch
                    if ($this->launchState === false)
                        $launch = "";
                    else if ($target)
                        $launch .= "Ext.getCmp('".$this->target."').add(new ".$matches[1]."()); \n";
                    else
                        $launch .= "var application = new ".$matches[1]."();";

                    // Buffer
                    $buffer = str_replace($matches[0], $launch, $buffer);
                    return true;
                }
            ],
            "render" => [
                "pattern" => "/Ext\.create\('(.*)', \{renderTo: Ext\.getBody\(\)\}\);/isU",
                "replacer" => function(string $launch, string &$buffer, array $matches, string $target = null): bool {
                    // Replace launch
                    if ($this->launchState === false)
                        $launch = "";
                    else if ($target)
                        $launch .= "Ext.getCmp('".$this->target."').add(new ".$matches[1]."()); \n";
                    else
                        $launch .= "var application = new ".$matches[1]."();";

                    // Buffer
                    $buffer = str_replace($matches[0], $launch, $buffer);
                    return true;
                }
            ],
        ];

        // Loop trough patterns
        foreach ($patterns as $pattern) {
            if (preg_match($pattern["pattern"], $buffer, $matches)) {
                if ($pattern["replacer"]($launch, $buffer, $matches, $this->target))
                    break;
            }
        }

        return $buffer;
    }
}