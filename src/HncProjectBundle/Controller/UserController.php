<?php

namespace HncProjectBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            ->add('lastname', TextType::class, ['label' => "Lastname :", 'attr' => ['class' => "form-control", 'placeholder' => "Last Name"]])
            ->add('email', EmailType::class, ['label' => "Email :", 'attr' => ['class' => 'form-control', 'placeholder' => "abcdefghij@email.com"]])
            ->add('birthday', BirthdayType::class, ['label' => "Birthday :", 'attr' => ['class' => 'form-control']])
            ->add('phonenumber', TelType::class, ['label' => "Phone number :", 'attr' => ['class' => 'form-control', 'placeholder' => "+44XXXXXXXXXXX"]])
            ->add('receiveMarketing', CheckboxType::class, ['label' => "Receive Marketing ?", 'attr' => ['class' => 'form-check-input']])
            ->add('password', RepeatedType::class, ['type' => PasswordType::class,
                'first_options' => ['label'=> 'Password', 'attr' => ['class' => "form-control", 'placeholder' => "********"]],
                'second_options' => ['label'=> 'Password confirmation', 'attr' => ['class' => "form-control", 'placeholder' => "*********"]]])
            ->add('Register', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'float : right']])
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