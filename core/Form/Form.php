<?php

namespace BDSCore\Form;

/**
 * Form class : Checks the matching of a form with a defined configuration
 * Classe Form : Vérifie la correspondance d'un formulaire avec une configuration définie
 *
 * @package BDSCore\Form
 */
class Form
{

    /**
     * @var string Name of the current method
     */
    private $method;

    /**
     * @var array Form configuration
     */
    private $configuration = [];

    /**
     * @var array Results
     */
    private $results = [];

    /**
     * Constructor of the class
     * Constructeur de la classe
     *
     * @param string $method Method (Example: "get", "post")
     *
     * @return void
     * @throws FormException
     */
    public function __construct(string $method = null) {
        if ($method != null) {
            $this->method = $method;
        } else {
            throw new FormException('The method must be specified as a parameter of the constructor of the Form() class.');
        }
    }

    /**
     * Defines a configuration for verifying the form
     * Définit une configuration pour la vérification du formulaire
     *
     * @param array $configuration Configuration
     *
     * @return void
     * @throws FormException
     */
    public function configure(array $configuration = null) {
        if ($configuration != null) {
            $this->configuration = $configuration;
        } else {
            throw new FormException('The configuration was not specified');
        }
    }

    /**
     * Checks the type of a variable
     * Vérifie le type d'une variable
     *
     * @param $item Element
     * @param string $type Type
     *
     * @return bool
     */
    private function checkType($item, string $type): bool {
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
     * Checks the length of a string
     * Vérifie la longueur d'une chaine de caractère
     *
     * @param string $element Element
     * @param array $method Method
     * @param array $configuration Configuration
     *
     * @return bool
     */
    private function checkLength(string $element, array $method, array $configuration): bool {
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
     * Converts the name of the method to a string and then returns the corresponding data
     * Convertit le nom de la méthode en chaine de caractère puis renvoi les données correspondantes
     *
     * @param string $method Name of method
     *
     * @return mixed Datas
     * @throws FormException
     */
    private function convertAndGetMethod(string $method) {
        $method = strtolower($method);
        if ($this->method == 'get') {
            $methodData = $_GET;
        } elseif ($this->method == 'post') {
            $methodData = $_POST;
        } else {
            throw new FormException('The form method is invalid or unsupported.');
        }

        return $methodData;
    }

    /**
     * Checks the form with the defined configuration
     * Vérifie le formulaire avec la configuration définie
     *
     * @return bool TRUE if ok
     * @throws FormException
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
                                if (!$this->checkLength($c, $method, $r)) {
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
                            if (isset($r['in_array'])) {
                                if (!in_array($method[$c], $r['in_array'])) {
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
                                'in_array',
                                'filter'
                            ]);
                            if (!empty($changes)) {
                                throw new FormException('A bad parameter was passed to the instantiation of the Form() class: "' . current($changes) . '".');
                            }
                            $this->results[$c] = $method[$c];
                        }
                    }
                }
                $i = $i + 1;
            }

            return true;
        } else {
            throw new FormException('The validate() function does not seem to be able to retrieve the configuration or method from the form.');

            return false;
        }
    }

    /**
     * Returns the results of the form. Variables are escaped if $convertHtmlSpecialChars is TRUE
     * Retourne les résultats du formulaire. Les variables sont échapées si $convertHtmlSpecialChars est TRUE
     *
     * @param bool $convertHtmlSpecialChars Results escaped ? Default is TRUE
     *
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