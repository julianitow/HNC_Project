<?php

namespace HncProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function registerAction()
    {
        return $this->render('HncProjectBundle:User:register.html.twig', array(
            // ...
        ));
    }

    public function loginAction()
    {
        return $this->render('HncProjectBundle:User:login.html.twig', array(
            // ...
        ));
    }

}
