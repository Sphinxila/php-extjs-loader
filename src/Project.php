<?php

namespace ExtJSLoader;

use ExtJSLoader\Model\Project as ProjectModel;

/**
 * Project loader (wrapper)
 * Class Project
 * @package ExtJSLoader
 */

class Project {
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
    public function setSubDir(string $subDir)
    {
        $this->subDir = $subDir;
    }

    /**
     * Set app js name (backward compatibility for ext3 etc.)
     * @param string $appJS
     */
    public function setAppJS(string $appJS)
    {
        $this->appJS = $appJS;
    }

    /**
     * @param bool $compiled
     * @param bool $compile
     */
    public function load(bool $compiled = false, bool $compile = false)
    {
        // Get project
        $this->loadProject($compiled, $compile);

        // Change target
        $this->handle();
    }

    /**
     * Get code
     * @param bool $minify
     * @return string
     */
    public function getCode(bool $minify = false): string
    {
        $buffer = "";
        foreach ($this->project()->getOrder() as $path) {
            $buffer .= $this->project()->getFile($path)["content"];
        }

        // Minify
        if ($minify) {
            $minfy = new Minify\JS();
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
    private function handler(string $filename, array &$info)
    {
        switch($filename) {
            // Application JS
            case $this->appJS:
                // Reset instance
                $launch = $this->appName . ".appInstance = this;\n";

                $pattern = '/Ext\.create\(\'(.*)\'\);/isU';
                if (preg_match($pattern, $info["content"], $matches)) {
                    // If new target is needed
                    if (!is_null($this->target)) {
                        $launch .= "Ext.getCmp('".$this->target."').add(new ".$matches[1]."()); \n";
                        $info["content"] = str_replace($matches[0], "", $launch);
                    } else {
                        $info["content"] = str_replace($matches[0], "", $launch . $matches[0]);
                    }
                }
                break;
        }
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
        if ($this->dirty === true)
            $this->write();

        return $this->project;
    }
}
