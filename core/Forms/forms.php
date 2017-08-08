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
     * @param $item
     * @param string $type
     * @return bool
     */
    private function checkType($item, string $type): bool {
        ($type == 'int') ? $type = 'integer' : null;
        ($type == 'str') ? $type = 'string' : null;
        ($type == 'bool') ? $type = 'boolean' : null;
        var_dump($item);
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
     * @param string $method
     * @return string
     * @throws FormsException
     */
    private function convertAndGetMethod(string $method) {
        $method = strtolower($method);
        if ($this->method == 'get') {
            $method = $_GET;
        } elseif ($this->method == 'post') {
            $method = $_POST;
        } else {
            throw new FormsException('The form method is invalid or unsupported.');
        }

        return $method;
    }

    /**
     * @return bool
     * @throws FormsException
     */
    public function validate(): bool {
        if (!empty($this->method) && !empty($this->configuration)) {
            $method = $this->convertAndGetMethod($this->method);
            $i = 0;
            foreach ($this->configuration as $c => $r) {
                ($c === $i) ? $c = $r : null;
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
                            }
                            if (isset($r['min-length']) || isset($r['max-length'])) {
                                if (!$this->checkLength($c, $method, $method[$c], $r)) {
                                    return false;
                                }
                            }
                            if (isset($r['value'])) {
                                if ($method[$c] !== $r['value']) {
                                    return false;
                                }
                            }
                            if (isset($r['keyIncludedIn'])) {
                                if (!array_key_exists($method[$c], $r['keyIncludedIn'])) {
                                    return false;
                                }
                            }
                            if (isset($r['filter'])) {
                                ($r['filter'] == 'email') ? $r['filter'] = FILTER_VALIDATE_EMAIL : null;
                                ($r['filter'] == 'url') ? $r['filter'] = FILTER_VALIDATE_URL : null;
                                if (!filter_var($method[$c], $r['filter'])) {
                                    return false;
                                }
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
                            $this->results[$c] = $method[$c];
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
     * @param bool $convertHtmlSpecialChars
     * @return array
     */
    public function getResults($convertHtmlSpecialChars = true): array {
        if ($convertHtmlSpecialChars) {
            $results = [];
            foreach ($this->results as $result) {
                (is_string($result)) ? array_push($results, htmlspecialchars($result)) : null;
            }

            return $results;
        } else {
            return $this->results;
        }
    }

}