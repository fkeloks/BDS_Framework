<?php

namespace BDSCore\Errors;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Errors
 * @package BDSCore\Errors
 */
class Errors
{

    /**
     * @param ResponseInterface $response
     * @param int $errorCode
     * @return void
     */
    public static function returnError(ResponseInterface $response, $errorCode = 500) {
        try {
            $template = new \BDSCore\Template\Twig($response);
            $template->render('errors/error' . $errorCode . '.twig');

            \Http\Response\send($response);
            exit();
        } catch (\Exception $e) {
            die('-[ Not allowed -]');
        }
    }

}