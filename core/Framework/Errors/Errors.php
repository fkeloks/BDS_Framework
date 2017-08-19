<?php

namespace BDSCore\Errors;

use BDSCore\Config\Config;
use Psr\Http\Message\ResponseInterface;

/**
 * Error Class: Returns an error in a view
 * Classe Error : Retourne une erreur dans une vue
 *
 * @package BDSCore\Errors
 */
class Errors
{

    /**
     * @var array Errors Message
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
     * Returns an error in a view if a corresponding view exists
     * Retourne une erreur dans une vue si une vue correspondante éxiste
     *
     * @param ResponseInterface $response Response
     * @param int $errorCode Error Code
     * @param bool $sendResponse If TRUE, the response is sent to the browser
     *
     * @return ResponseInterface|static
     */
    public static function returnError(ResponseInterface $response, $errorCode = 500, $sendResponse = true) {

        if (file_exists(Config::getDirectoryRoot('/app/views/errors/error' . $errorCode . '.twig'))) {
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
            \BDSCore\Application\App::send($response);
            exit();
        } else {
            return $response;
        }

    }

}