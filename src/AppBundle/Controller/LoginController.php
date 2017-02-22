<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class LoginController extends BaseController
{
    public function indexAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            return $this->createJsonResponse(array(
                'error' => 'login error',
                'message' => "last login is {$lastUsername}"
            ));
        }
        return $this->createJsonResponse(
            array(
                'code' => 1,
                'message' => 'please login'
            )
        );
    }

    protected function getExampleService()
    {
        return $this->biz['example_service'];
    }
}
