<?php

namespace BDSCore\Security;

/**
 * Class Login
 * @package BDSCore\Security
 */
class Login
{

    /**
     * @param string $authPage
     */
    public static function renderLogin(string $authPage) {
        $template = new \BDSCore\Twig\Template();
        $template->render($authPage);
        exit();
    }

    public function checkForm() {
        $authAccounts = \BDSCore\Config\Config::getSecurityConfig('authAccounts');
        $form = new \BDSCore\Forms\Forms('post', [
            'username' => [
                'keyIncludedIn' => $authAccounts
            ],
            'password' => [
                'ValueIncludedIn' => $authAccounts
            ]
        ]);
        if ($form->validate()) {
            $results = $form->getResults();
            if ($authAccounts[$results['username']] == $results['password']) {
                $_SESSION['auth'] = true;
                header('Location: /');
            } else {
                header('Location: login');
            }
        } else {
            header('Location: login');
        }
    }

}