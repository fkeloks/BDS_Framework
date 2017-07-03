<?php

namespace BDSCore;

/**
 * Class Errors
 * @package BDSCore
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

        }
        exit();
    }

}