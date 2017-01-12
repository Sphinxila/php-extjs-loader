<?php

namespace ExtJsLoader\Exception;

class SerializeException extends \Exception {
    /** @var string */
    private $key;

    /**
     * SerializeException constructor.
     * @param string $key
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct(string $key, string $message, int $code = 0, \Exception $previous = null)
    {
        $this->key = $key;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Path
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }
}
