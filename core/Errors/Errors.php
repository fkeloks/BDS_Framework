<?php

namespace BDSCore\Errors;

use BDSCore\Config\Config;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Errors
 * @package BDSCore\Errors
 */
class Errors
{

    /**
     * @var array
     */
    private static $errors = [
        '400' => 'Failure of HTTP scan.',
        '401' => 'Bad username or password in the .htaccess.',
        '402' => 'The customer must reformulate his request with the correct payment data.',
        '403' => 'Prohibited Application.',
        '404' => 'Page not found.',
        '405' => 'Unauthorized method.',
        '500' => 'Internal server error.',
        '501' => 'The server does not support the requested service.',
        '502' => 'Bad Gateway.',
        '503' => 'Service Unavailable.',
        '504' => 'Too much time to answer.',
        '505' => 'HTTP version not supported.'
    ];

    /**
     * @param ResponseInterface $response
     * @param int $errorCode
     * @param bool $sendResponse
     * @return ResponseInterface
     */
    public static function returnError(ResponseInterface $response, $errorCode = 500, $sendResponse = true) {

        if (file_exists(Config::getDirectoryRoot('/app/views//errors/error' . $errorCode . '.twig'))) {
            $template = new \BDSCore\Template\Twig($response);
            $response = $template->render('errors/error' . $errorCode . '.twig');
        } else {
            if (isset(self::$errors[$errorCode])) {
                $error = self::$errors[$errorCode];
                $response->getBody()->write('Error ' . $errorCode . '<br>' . $error);
            } else {
                $response->getBody()->write('Error ' . (string)$errorCode);
            }
        }

        $response = $response->withStatus($errorCode);

        if ($sendResponse) {
            \Http\Response\send($response);
            exit();
        } else {
            return $response;
        }

    }

}