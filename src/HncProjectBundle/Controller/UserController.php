<?php

namespace HncProjectBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use HncProjectBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class UserController extends Controller
{
    public function registerAction(Request $request)
    {
        $user = new User();
        $registerFormBuider = $this->get('form.factory')->createBuilder(FormType::class, $user, ['allow_extra_fields' => true]);
        $registerFormBuider
            ->add('firstname', TextType::class, ['label' => "Firstname :", 'attr' => ['class' => "form-control", 'placeholder' => "First Name"]])
            ->add('lastname', TextType::class, ['label' => "Lastname", 'attr' => ['class' => "form-control", 'placeholder => Last Name']])
            ->add('email', EmailType::class)
            ->add('birthday', BirthdayType::class)
            ->add('phonenumber', TelType::class)
            ->add('receiveMarketing', CheckboxType::class)
            ->add('password', RepeatedType::class)
            ->add('Register', SubmitType::class)
            ;
        $registerForm = $registerFormBuider->getForm();
        $registerForm->handleRequest($request);


        return $this->render('@HncProject\User\register.html.twig', ['registerForm' => $registerForm->createView()]);
    }

    public function loginAction()
    {
        return $this->render('@HncProject\User\login.html.twig');
    }

    public function logoutAction()
    {
        return $this->render('@HncProject\User\logout.html.twig');
    }
}