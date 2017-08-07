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
        $form = new \BDSCore\Forms\Forms('post');
        $form->configure([
            'username' => [
                'keyIncludedIn' => $authAccounts
            ],
            'password' => [
                'min-length' => 3
            ]
        ]);
        if ($form->validate()) {
            $results = $form->getResults();
            if ((string) $authAccounts[$results['username']] == (string) $results['password']) {
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