<?php

namespace BDSHelpers\Validator;

/**
 * Class Validator : Checks data
 * Classe Validator : Vérifie une donnée
 *
 * @package BDSHelpers\Validator
 */
class Validator
{

    /**
     * Checks the length of a string
     * Vérifie la longueur d'une chaine de caractère
     *
     * @param string $element Element
     * @param int $min Minimum
     * @param int $max Maximum
     *
     * @return bool
     */
    public static function checkLength(string $element, int $min, int $max): bool {
        if (strlen($element) < $min || strlen($element) > $max) {
            return false;
        }

        return true;
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
    public static function checkType($item, string $type): bool {
        ($type == 'int') ? $type = 'integer' : null;
        ($type == 'str') ? $type = 'string' : null;
        ($type == 'bool') ? $type = 'boolean' : null;
        if (gettype($item) != $type) {
            return false;
        }

        return true;
    }

    /**
     * Verifies that a variable matches a filter
     * Vérifie qu'une variable correspond à un filtre
     *
     * @param $item Element
     * @param $filter Filter
     *
     * @return bool
     */
    public static function checkFilter($item, $filter): bool {
        ($filter == 'email') ? $filter = FILTER_VALIDATE_EMAIL : null;
        ($filter == 'url') ? $filter = FILTER_VALIDATE_URL : null;
        if (!filter_var($item, $filter)) {
            return false;
        }

        return true;
    }

}