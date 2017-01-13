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

use ExtJSLoader\Model\Project as ProjectModel;
use ExtJSLoader\Parser\ExtParser;

/**
 * Project loader (wrapper)
 * Class Project
 * @package ExtJSLoader
 */

class Project {
    // Use default parser
    use ExtParser;

    /** @var string */
    private $appName;

    /** @var string */
    private $path;

    /** @var null|string */
    private $xvtPath;

    /** @var bool */
    private $dirty = false;

    /** @var string */
    private $subDir = "app";

    /** @var string */
    private $appJS = "app.js";

    /** @var string */
    private $target;

    /** @var \ExtJSLoader\Model\Project */
    private $project;

    /**
     * Project constructor.
     * @param string $appName
     *  Name of the Application (must be the same as in the architect project)
     * @param string $path
     *  Path to the raw sencha architect project
     * @param string|null $xvt
     *  XVT Path is the path that we define for the compiled/cached format of the ext project
     * @param string $target
     *  Target 'div' element in html where the project needs to be rendered
     */
    public function __construct(string $appName, string $path, string $xvt = null, string $target = null)
    {
        // Project name
        $this->appName = $appName;

        // Project path
        $this->path = $path;

        // XVT Path
        $this->xvtPath = $xvt;

        // Target
        $this->target = $target;
    }

    /**
     * Set sub directory
     * @param string $subDir
     */
    public function setSubDir(string $subDir): void
    {
        $this->subDir = $subDir;
        return;
    }

    /**
     * Set app js name (backward compatibility for ext3 etc.)
     * @param string $appJS
     */
    public function setAppJS(string $appJS): void
    {
        $this->appJS = $appJS;
        return;
    }

    /**
     * @param bool $compiled
     * @param bool $compile
     */
    public function load(bool $compiled = false, bool $compile = false): void
    {
        // Get project
        $this->loadProject($compiled, $compile);

        // Change target
        $this->handle();
        return;
    }

    /**
     * Get code
     * @param bool $compress
     * @return string
     */
    public function getCode(bool $compress = false): string
    {
        $buffer = "";
        foreach ($this->project()->getOrder() as $path) {
            $buffer .= $this->project()->getFile($path)["content"];
        }

        // Minify
        if ($compress) {
            $minfy = new \MatthiasMullie\Minify\JS();
            $minfy->add($buffer);

            // Output
            $buffer = $minfy->minify();
        }

        return $buffer;
    }

    /**
     * Handle
     */
    private function handle(): void
    {
        // File
        foreach ($this->project()->getFiles() as $path => &$info) {
            // Basename
            $file = basename($path);

            // Handler
            $this->handler($file, $info);

            // Parse
            ProjectParser::parse($path, $info['content']);
        }
        return;
    }

    /**
     * @param string $filename
     * @param array $info
     */
    private function handler(string $filename, array &$info): void
    {
        // Build Method name
        $method = "parse".str_replace(".", "", $filename);

        // Check method parse exist
        if (method_exists($this,$method)) {
            $this->$method($info['content'], $info);
        }
        return;
    }

    /**
     * Get project
     * @return ProjectModel
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Load XVT
     * @return ProjectModel
     */
    private function xvt(): ProjectModel
    {
        $project = new ProjectModel();

        if (file_exists($this->xvtPath)) {
            $buffer = file_get_contents($this->xvtPath);
            $raw = json_decode($buffer, true);
            $project->fromArray($raw);
        }

        return $project;
    }

    /**
     * Get project
     * @return ProjectModel
     */
    private function project(): ProjectModel
    {
        // Sencha project loader
        $loader = new ProjectLoader(
            $this->appName,
            $this->path,
            $this->subDir,
            $this->appJS
        );

        // Load
        $loader->load();
        return $loader->getProject();
    }

    /**
     * Write
     * @return bool
     */
    private function write(): bool
    {
        // Don't write if no path defined
        if (is_null($this->xvtPath))
            return false;

        // Raw project
        $raw = $this->project->toArray();
        return file_put_contents($this->xvtPath, json_encode($raw));
    }

    /**
     * Get project
     * @param bool $compiled
     *  Try to load project from compiled XVT file
     * @param bool $compile
     *  Compile and write to XVT path
     * @return ProjectModel
     */
    public function loadProject(bool $compiled = false, bool $compile = false): ProjectModel
    {
        // Load from XVT File
        if ($compiled && file_exists($this->xvtPath)) {
            // No dirty flag
            $this->dirty = false;

            // Initialize from XVT
            $this->project = $this->xvt();
        } else {
            // Possibly dirty...
            $this->dirty = true;

            // Initialize from raw sencha project
            $this->project = $this->project();
        }

        // Dump
        if ($this->dirty === true && $compile)
            $this->write();

        return $this->project;
    }
}
