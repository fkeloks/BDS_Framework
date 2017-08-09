<?php

namespace BDSCore\Security;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Login
 * @package BDSCore\Security
 */
class Login
{

    /**
     * @param ResponseInterface $response
     * @param string $authPage
     */
    public static function renderLogin(ResponseInterface $response, string $authPage) {
        $template = new \BDSCore\Template\Twig($response);
        return $template->render($authPage);
    }

    /**
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public static function checkForm(ResponseInterface $response): ResponseInterface {
        $authAccounts = \BDSCore\Config\Config::getSecurityConfig('authAccounts');
        $form = new \BDSCore\Form\Form('post');
        $form->configure([
            'username' => [
                'keyIncludedIn' => $authAccounts
            ],
            'password' => [
                'min-length' => 3
            ]
        ]);
        if ($form->validate()) {
            $results = $form->getResults(false);
            if ((string) $authAccounts[$results['username']] == (string) $results['password']) {
                $_SESSION['auth'] = true;
                return $response->withHeader('Location', '/');
            } else {
                return $response->withHeader('Location', 'login');
            }
        } else {
            return $response->withHeader('Location', '/login');
        }
    }

}