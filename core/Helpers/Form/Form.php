<?php

namespace BDSHelpers\Form;

use BDSHelpers\Validator\Validator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Form class : Checks the matching of a form with a defined configuration
 * Classe Form : Vérifie la correspondance d'un formulaire avec une configuration définie
 *
 * @package BDSHelpers\Form
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
     * @var ServerRequestInterface Request
     */
    private $request;

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
        $request = $this->request;

        if ($this->method == 'get') {
            $methodData = $request->getQueryParams();
        } elseif ($this->method == 'post') {
            $methodData = $request->getParsedBody();
        } else {
            throw new FormException('The form method is invalid or unsupported.');
        }

        return $methodData;
    }

    /**
     * Checks the form with the defined configuration
     * Vérifie le formulaire avec la configuration définie
     *
     * @param ServerRequestInterface $request Request
     *
     * @return bool TRUE if ok
     * @throws FormException
     */
    public function validate(ServerRequestInterface $request): bool {
        $this->request = $request;
        if (!empty($this->method) && !empty($this->configuration)) {
            $method = $this->convertAndGetMethod($this->method);

            foreach ($this->configuration as $c => $r) {
                $item = $method[$c];
                if (is_string($r)) {
                    if (!Validator::checkType($item, $r)) {
                        return false;
                    }
                    $this->results[$c] = $item;
                } elseif (is_array($r)) {
                    if (isset($r['type'])) {
                        if (!Validator::checkType($item, $r['type'])) {
                            return false;
                        }
                    }
                    if (isset($r['min-length']) || isset($r['max-length'])) {
                        $min = (isset($r['min-length'])) ? $r['min-length'] : 0;
                        $max = (isset($r['max-length'])) ? $r['max-length'] : 100;
                        if (!Validator::checkLength($item, $min, $max)) {
                            return false;
                        }
                    }
                    if (isset($r['value'])) {
                        if ($item !== $r['value']) {
                            return false;
                        }
                    }
                    if (isset($r['keyIncludedIn'])) {
                        if (!array_key_exists($item, $r['keyIncludedIn'])) {
                            return false;
                        }
                    }
                    if (isset($r['in_array'])) {
                        if (!in_array($item, $r['in_array'])) {
                            return false;
                        }
                    }
                    if (isset($r['filter'])) {
                        if (!Validator::checkFilter($item, $r['filter'])) {
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
                    $this->results[$c] = $item;
                }
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