<?php

namespace BDSCore\Errors;

/**
 * Class Errors
 * @package BDSCore\Errors
 */
class Errors
{

    /**
     * @param int $errorCode
     */
    public static function returnError($errorCode = 500) {
        try {
            $template = new \BDSCore\Twig\Template();
            $template->render('errors/error' . $errorCode . '.twig');
        } catch (\Exception $e) {
            die('-[ Not allowed -]');
        }
        exit();
    }

}