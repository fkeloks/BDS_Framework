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
     * @param $item
     * @param $type
     * @return bool
     */
    private function checkType($item, $type): bool {
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
                            if (isset($r['min-length'])) {
                                if (strlen($method[$c]) < $r['min-length']) {
                                    return false;
                                }
                                $this->results[$c] = $method[$c];
                            }
                            if (isset($r['max-length'])) {
                                if (strlen($method[$c]) > $r['max-length']) {
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
                            if (isset($r['valueIncludedIn'])) {
                                if (!in_array($method[$c], $r['valueIncludedIn'])) {
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
                                'valueIncludedIn'
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