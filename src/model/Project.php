<?php

namespace ExtJSLoader\Model;
use ExtJSLoader\Facade\Serializable;

/**
 * Model
 * Class Project
 * @package ExtJSLoader\Model
 */
class Project {
    // Serializable
    use Serializable;

    /** @var string */
    private $path;

    /** @var string */
    private $appPath;

    /** @var string */
    private $appName;

    /** @var string[] */
    private $paths = [];

    /** @var array */
    private $files = [];

    /** @var array */
    private $order = [];

    /** @var array */
    private $supplies = [];

    /** @var string */
    private $appJS;

    /** @var string */
    private $requirementsPattern;

    /** @var string */
    private $definePattern;

    /**
     * Serializable
     * @var array
     */
    public $serializable = [
        "path",
        "appPath",
        "appName",
        "paths",
        "files",
        "order",
        "supplies",
        "appJS",
        "requirementsPattern",
        "definePattern"
    ];

    /**
     * Set path
     * @param $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
        return;
    }

    /**
     * Path size
     * @return int
     */
    public function pathSize(): int
    {
        return sizeof($this->paths);
    }

    /**
     * Set app path
     * @param $path
     */
    public function setAppPath($path): void
    {
        $this->appPath = $path;
        return;
    }

    /**
     * @param $name
     */
    public function setAppName($name): void
    {
        $this->appName = $name;
        return;
    }

    /**
     * Set paths
     * @param array $paths
     */
    public function setPaths(array $paths): void
    {
        $this->paths = $paths;
        return;
    }

    /**
     * @param string $path
     */
    public function addPath(string $path): void
    {
        $this->paths[] = $path;
        return;
    }

    /**
     * Set files
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
        return;
    }

    /**
     * Set file (Sym: Project::addFile())
     * @param string $path
     * @param array $info
     */
    public function setFile(string $path, array $info): void
    {
        $this->addFile($path, $info);
        return;
    }

    /**
     * Add file
     * @param string $path
     * @param array $info
     */
    public function addFile(string $path, array $info): void
    {
        $this->files[$path] = $info;
        return;
    }

    /**
     * Set order
     * @param array $order
     */
    public function setOrder(array $order): void
    {
        $this->order = $order;
        return;
    }

    /**
     * Add order
     * @param string $order
     */
    public function addOrder(string $order): void
    {
        $this->order[] = $order;
        return;
    }

    /**
     * Check if order exists
     * @param string $order
     * @return bool
     */
    public function orderExists(string $order): bool
    {
        return in_array($order, $this->order) === true;
    }

    /**
     * Set supplies
     * @param array $supplies
     */
    public function setSupplies(array $supplies): void
    {
        $this->supplies = $supplies;
        return;
    }

    /**
     * Add supply
     * @param string $supply
     * @param string $path
     */
    public function addSupply(string $supply, string $path): void
    {
        $this->supplies[$supply] = $path;
        return;
    }

    /**
     * Get path by supply
     * @param string $supply
     * @return string
     */
    public function getPathBySupply(string $supply): string
    {
        if (array_key_exists($supply, $this->supplies))
            return $this->supplies[$supply];
        return "";
    }

    /**
     * Check if supply exist
     * @param string $supply
     * @return bool
     */
    public function supplyExist(string $supply): bool
    {
        return array_key_exists($supply, $this->supplies);
    }

    /**
     * @param string $name
     */
    public function setAppJS(string $name): void
    {
        $this->appJS = $name;
        return;
    }

    /**
     * Get root path of Architect project
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get application path
     * @return string
     */
    public function getAppPath(): string
    {
        return $this->appPath;
    }

    /**
     * Get application name
     * @return string
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * Get paths
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get files
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * File exists
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        return array_key_exists($path, $this->files);
    }

    /**
     * Get file
     * @param string $path
     * @return array
     */
    public function getFile(string $path): array
    {
        if ($this->fileExists($path))
            return $this->files[$path];
        return [];
    }

    /**
     * Get order
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * Get supplies
     * @return array
     */
    public function getSupplies(): array
    {
        return $this->supplies;
    }

    /**
     * App JS
     * @return string
     */
    public function getAppJS(): string
    {
        return $this->appJS;
    }

    /**
     * Used pattern
     * @param string $pattern
     */
    public function setRequirementPattern(string $pattern): void
    {
        $this->requirementsPattern = $pattern;
        return;
    }

    /**
     * Used pattern
     * @param string $pattern
     */
    public function setDefinePattern(string $pattern): void
    {
        $this->definePattern = $pattern;
        return;
    }
}