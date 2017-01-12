<?php

namespace ExtJSLoader;
use ExtJsLoader\Exception\ProjectLoaderException;
use ExtJSLoader\Model\Project;

/**
 * Class Project
 * @package ExtJSLoader
 */
class ProjectLoader {
    /** @var string */
    private $requirementsPattern = "/requires: \[(.*?)\]/s";

    /** @var string */
    private $definePattern = "/Ext.define\('(.*?)'/s";

    /**
     * ProjectLoader constructor.
     * @param string $name
     *  Name of the project (We need this if we wana replace the destination of the project)
     * @param string $path
     *  Path (Directory of project)
     * @param string $subDir
     *  Directory (app relative from path)
     * @param string $appJS
     *  Name of the main application file (app.js default)
     */
    public function __construct(
        string $name,
        string $path,
        string $subDir = "app",
        string $appJS = "app.js"
    ) {
        // Project
        $this->project = new Project();

        // Set base information
        $this->project->setPath($path);
        $this->project->setAppPath($path . "/" . $subDir . "/");
        $this->project->setAppJS($appJS);
        $this->project->setAppName($name);
    }

    /**
     * @param string $pattern
     */
    public function setRequirementPattern(string $pattern): void
    {
        $this->requirementsPattern = $pattern;
        return;
    }


    /**
     * @param string $pattern
     */
    public function setDefinePattern(string $pattern): void
    {
        $this->definePattern = $pattern;
        return;
    }

    /**
     * Load project
     */
    public function load(): void
    {
        if ($this->list()) {
            // Read source files
            $this->source();

            // Resolve conflicts
            $this->resolve();
        }
        return;
    }

    /**
     * Get project
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * Load project
     * @return bool
     */
    private function list(): bool
    {
        // Directory iterator
        $di = new \RecursiveDirectoryIterator($this->project->getAppPath());

        // Retrieve all files
        foreach ($it = new \RecursiveIteratorIterator($di) as $filename => $file) {
            // Ignore dots
            if ($it->isDot()) {
                continue;
            }

            // We just need js files
            $info = pathinfo($filename);
            if ($info['extension'] != "js") {
                continue;
            }

            $this->project->addPath($filename);
        }

        // App JS
        $appJS = $this->project->getPath() . "/" . $this->project->getAppJS();
        if (file_exists($appJS)) {
            $this->project->addPath($appJS);
        }

        return sizeof($this->project->pathSize()) > 1 ? true : false;
    }

    /**
     * Source
     * @throws ProjectLoaderException
     */
    private function source(): void
    {
        foreach ($this->project->getPaths() as $path) {
            // Check if file exists
            if (!file_exists($path))
                throw new ProjectLoaderException($path, "File not exists", 404);

            // Content
            $content = file_get_contents($path);

            // Check if file is empty or not readable
            if (strlen($content) == 0)
                throw new ProjectLoaderException($path, "Invalid file, empty or permission denied", 500);

            // Supplies
            $supplies = $this->supplies($content);

            // Add
            $this->project->addFile($path, [
                // path and content
                "content" => $content,
                "path" => $path,

                // Dependencies and
                "requirements" => $this->requirements($content),
                "supplies" => $supplies,

                // Some informations about the source file
                "mtime" => filemtime($path),
                "size" => filesize($path),
            ]);

            // Reference
            foreach ($supplies as $supply) {
                if ($this->project->supplyExist($supply))
                    throw new ProjectLoaderException(
                        $path,
                        "Define (Supply) redeclaration (".$supply.") already defined in " .
                        $this->project->getPathBySupply($supply),
                        700
                    );

                // Add provided defines by file
                $this->project->addSupply($supply, $path);
            }
        }
        return;
    }

    /**
     * @param string $buffer
     * @return array
     */
    private function requirements(string $buffer): array
    {
        // Initial
        $requires = [];
        $pregBuffer = [];

        // Parse requirements
        preg_match_all($this->requirementsPattern, $buffer, $pregBuffer);

        // Last used pattern
        $this->project->setRequirementPattern($this->requirementsPattern);

        // If something found
        if (is_array($pregBuffer[1]) && sizeof($pregBuffer[1]) > 0) {
            // Strip
            $temp = $pregBuffer[1][0];
            $temp = str_replace('\'', '', trim($temp));
            $temp = preg_replace('/\s+/', '', $temp);

            // Split
            $requires = explode(',', $temp);

            // Free
            unset($temp);
        }

        return $requires;
    }

    /**
     * Get defines
     * @param string $buffer
     * @return array
     */
    private function supplies(string $buffer): array
    {
        // Initial
        $defines = [];
        $pregBuffer = [];

        // Parse defines
        preg_match_all($this->definePattern, $buffer, $pregBuffer);

        // Last used pattern
        $this->project->setDefinePattern($this->definePattern);

        // If something found
        if (is_array($pregBuffer[1]) && sizeof($pregBuffer[1]) > 0) {
            // Strip
            $temp = $pregBuffer[1][0];
            $temp = str_replace('\'', '', trim($temp));
            $temp = preg_replace('/\s+/', '', $temp);

            $defines[] = $temp;

            // Free
            unset($temp);
        }

        return $defines;
    }

    /**
     * Sort requirements
     */
    private function resolve(): void
    {
        // Loop over files
        foreach ($this->project->getFiles() as $key => $info) {
            $this->resolveConflicts($key, $info);
        }
        return;
    }

    /**
     * @param string $key
     * @param array $info
     */
    private function resolveConflicts(string $key, array $info): void
    {
        // Requirements
        $requirements = $info["requirements"];

        // Already here
        if ($this->project->orderExists($key)) {
            return;
        }

        // Add requirements
        foreach ($requirements as $require) {
            if ($parent = $this->project->getPathBySupply($require)) {
                // Just add if not already added
                if (!$this->project->orderExists($parent)) {
                    // Resolve requirements for parent
                    $this->resolveConflicts($parent, $this->project->getFile($parent));
                }
            }
        }

        // Add self
        $this->project->addOrder($key);
        return;
    }
}