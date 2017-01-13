<?php

namespace ExtJSLoader\Exception;

class ProjectLoaderException extends \Exception {
    /** @var string */
    private $path;

    /**
     * ProjectLoaderExceptions constructor.
     * @param string $path
     * @param string $message
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(string $path, string $message, int $code = 0, \Exception $previous = null)
    {
        $this->path = $path;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Path
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
