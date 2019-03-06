<?php

namespace HncProjectBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

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
            ->add('receiveMarketing', CheckboxType::class, ['label' => "Receive Marketing ?", 'required' => false, 'attr' => ['class' => 'form-check-input']])
            ->add('password', RepeatedType::class, ['type' => PasswordType::class,
                'first_options' => ['label'=> 'Password', 'attr' => ['class' => "form-control", 'placeholder' => "********"]],
                'second_options' => ['label'=> 'Password confirmation', 'attr' => ['class' => "form-control", 'placeholder' => "*********"]]])
            ->add('Register', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'float : right']])
            ;
        $registerForm = $registerFormBuider->getForm();
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid())
        {
            $user = $registerForm->getData();
            $passwordEncoder = $this->get('security.password_encoder');
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($user);

            try
            {
                $manager->flush();
            }
            catch (\PDOException $e)
            {
                $error = "PDOException";
            }
            catch (UniqueConstraintViolationException $e)
            {
                $error = "UniqueConstraintViolationException";
            }
        }


        return $this->render('@HncProject\User\register.html.twig', ['registerForm' => $registerForm->createView()]);
    }

    public function loginAction(Request $request)
    {
        $user = new User();
        $loginFormBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user, ['allow_extra_fields' => true]);
        $loginFormBuilder
            ->add('email', EmailType::class, ['label' => "Email :", 'attr' => ['class' => 'form-control', 'placeholder' => "username@email.com"]])
            ->add('password', PasswordType::class, ['label' => "Password : ", 'attr' => [ 'class' => 'form-control', 'placeholder' => "********"]])
            ->add('login', SubmitType::class, ['label' => "Login", 'attr' => ['class' => 'btn btn-primary']])
            ;
        $loginForm = $loginFormBuilder->getForm();
        $loginForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm-> isValid())
        {
            $user = $loginForm->getData();
            $enteredPassword = $user->getPassword();
            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('HncProjectBundle:User');
            $passwordEncoder = $this->container->get('security.password_encoder');
            $hashedPassword = $repositoryUsers->getHash($user->getEmail());
            //verification du resultat de la requete
            if ($hashedPassword != "NoResultException")
            {
                $user->setPassword($hashedPassword['password']);
            }
            else
            {
                $loginError = "NoResultException";
            }
            if ($passwordEncoder->isPasswordValid($user, $enteredPassword))
            {
                $user = $repositoryUsers->loadUserByEmail($user->getEmail());
                $user->setIsActive(true);
                $token = new UsernamePasswordToken($user, $user->getEmail(), 'main', $user->getRoles());
                $token->setUser($user);
                $this->get('security.token_storage')->setToken($token);
                $this->get('session')->set('token', $token);
                $this->get('session')->migrate();
                $this->get('session')->set('_security_main', $user->serialize($token));
                $this->get('session')->save();
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
                $redirect_response = $this->redirectToRoute("hnc_project_homepage");
                echo $redirect_response;
            }
            else
            {
                echo "ERROR LOGIN";
            }
        }

        return $this->render('@HncProject\User\login.html.twig', ['loginForm' => $loginForm->createView()]);
    }

    public function logoutAction()
    {

        return $this->render('@HncProject\User\logout.html.twig');
    }

    public function user_settingsAction(Request $request)
    {
        $user = new User();
        $manager = $this->getDoctrine()->getManager();
        $repositoryUsers = $manager->getRepository('HncProjectBundle:User');
        

        return $this->render('@HncProject\User\user_settings.html.twig');
    }
}