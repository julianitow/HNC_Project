<?php

namespace HncProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        if ( null != $request->getSession()->get('token'))
        {
            var_dump($request->getSession()->get('token')->getUser());
            var_dump($request->getSession()->get('_security_main'));
            $token = $token = $this->get('session')->get('token');
            $this->get('security.token_storage')->setToken($token);
        }

        return $this->render('@HncProject/Default/index.html.twig');
    }
}
