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
    private $configuration = [];

    /**
     * @var array
     */
    private $results = [];

    /**
     * Forms constructor.
     * @param string|null $method
     * @throws FormsException
     */
    public function __construct(string $method = null) {
        if ($method != null) {
            $this->method = $method;
        } else {
            throw new FormsException('The method and the configuration must be specified as a parameter of the constructor of the Forms() class.');
        }
    }

    /**
     * @param array|null $configuration
     * @throws FormsException
     */
    public function configure(array $configuration = null) {
        if ($configuration != null) {
            $this->configuration = $configuration;
        } else {
            throw new FormsException('The configuration was not specified');
        }
    }

    /**
     * @param string $item
     * @param string $type
     * @return bool
     */
    private function checkType(string $item, string $type): bool {
        ($type == 'int') ? $type = 'integer' : null;
        ($type == 'str') ? $type = 'string' : null;
        ($type == 'bool') ? $type = 'boolean' : null;
        if (gettype($item) != $type) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $element
     * @param array $method
     * @param string $item
     * @param array $configuration
     * @return bool
     */
    private function checkLength(string $element, array $method, string $item, array $configuration): bool {
        if (isset($configuration['min-length'])) {
            if (strlen($method[$element]) < $configuration['min-length']) {
                return false;
            }
        }
        if (isset($configuration['max-length'])) {
            if (strlen($method[$element]) > $configuration['max-length']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws FormsException
     */
    public function validate(): bool {
        if (!empty($this->method) && !empty($this->configuration)) {
            ($this->method == 'get' || $this->method == 'GET') ? $method = $_GET : null;
            ($this->method == 'post' || $this->method == 'POST') ? $method = $_POST : null;
            $i = 0;
            foreach ($this->configuration as $c => $r) {
                if ($c === $i) {
                    $c = $r;
                }
                if (!isset($method[$c])) {
                    return false;
                } else {
                    if ($c == $r) {
                        $this->results[$c] = $method[$c];
                    } else {
                        if (is_string($r)) {
                            if (!$this->checkType($method[$c], $r)) {
                                return false;
                            }
                            $this->results[$c] = $method[$c];
                        } elseif (is_array($r)) {
                            if (isset($r['type'])) {
                                if (!$this->checkType($method[$c], $r['type'])) {
                                    return false;
                                }
                                $this->results[$c] = $method[$c];
                            }
                            if (isset($r['min-length']) || isset($r['max-length'])) {
                                if (!$this->checkLength($c, $method, $method[$c], $r)) {
                                    return false;
                                }
                                $this->results[$c] = $method[$c];
                            }
                            if (isset($r['value'])) {
                                if ($method[$c] !== $r['value']) {
                                    return false;
                                }
                                $this->results[$c] = $method[$c];
                            }
                            if (isset($r['keyIncludedIn'])) {
                                if (!array_key_exists($method[$c], $r['keyIncludedIn'])) {
                                    return false;
                                }
                                $this->results[$c] = $method[$c];
                            }
                            if (isset($r['filter'])) {
                                ($r['filter'] == 'email') ? $r['filter'] = FILTER_VALIDATE_EMAIL : null;
                                ($r['filter'] == 'url') ? $r['filter'] = FILTER_VALIDATE_URL : null;
                                if (!filter_var($method[$c], $r['filter'])) {
                                    return false;
                                }
                                $this->results[$c] = $method[$c];
                            }
                            $changes = array_diff(array_keys($r), [
                                'type',
                                'min-length',
                                'max-length',
                                'value',
                                'keyIncludedIn',
                                'filter',
                            ]);
                            if (!empty($changes)) {
                                throw new FormsException('A bad parameter was passed to the instantiation of the Form() class: "' . current($changes) . '".');
                            }
                        }
                    }
                }
                $i = $i + 1;
            }

            return true;
        } else {
            throw new FormsException('The validate() function does not seem to be able to retrieve the configuration or method from the form.');

            return false;
        }
    }

    /**
     * @return array
     */
    public function getResults(): array {
        return $this->results;
    }

}