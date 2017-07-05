<?php

namespace BDSCore\Forms;

/**
 * Class Forms
 * @package BDSCore\Forms
 */
class Forms
{

    /**
     * @var string
     */
    private $method;
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var array
     */
    private $results = [];

    /**
     * Forms constructor.
     * @param string|null $method
     * @param array|null $configuration
     * @throws FormsException
     */
    public function __construct(string $method = null, array $configuration = null) {
        if ($method != null && $configuration != null) {
            $this->method = $method;
            $this->configuration = $configuration;

            return true;
        } else {
            throw new FormsException('The method and the configuration must be specified as a parameter of the constructor of the Forms() class.');
        }
    }

    /**
     * @return bool
     * @throws FormsException
     */
    public function validate(): bool {
        if (!empty($this->method) && !empty($this->configuration)) {
            ($this->method == 'get' || $this->method == 'GET') ? $method = $_GET : null;
            ($this->method == 'post' || $this->method == 'POST') ? $method = $_POST : null;
            foreach ($this->configuration as $c => $r) {
                ($c === 0) ? $c = $r : null;
                if (!isset($method[$c])) {
                    return false;
                } else {
                    if ($c == $r) {
                        $this->results[$c] = $method[$c];
                    } else {
                        ($r == 'int') ? $r = 'integer' : null;
                        ($r == 'str') ? $r = 'string' : null;
                        ($r == 'bool') ? $r = 'boolean' : null;
                        if (gettype($method[$c]) != $r) {
                            return false;
                        } else {
                            $this->results[$c] = $method[$c];
                        }
                    }
                }
            }

            return true;
        } else {
            throw new FormsException('The validate () function fails to retrieve the class information.');
        }
    }

    /**
     * @return array
     */
    public function getResults(): array {
        return $this->results;
    }

}