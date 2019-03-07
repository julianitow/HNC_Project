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
        $code = null;
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
                $this->get('session')->set('user_id', $user->getId());
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
                $code ="LOGIN_FAILED_O1";
            }
        }

        return $this->render('@HncProject\User\login.html.twig', ['loginForm' => $loginForm->createView(), 'code' => $code]);
    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $user_id = $session->get('user_id');
        $user = $this->get_user($user_id);
        $this->get('session')->set('_security_main', $user->unserialize($user->serialize()));
        $session->invalidate();
        $redirection_response = $this->redirectToRoute('login');
        echo 'Logging out...' . $redirection_response;
        return $redirection_response;
    }

    public function get_user($user_id)
    {
        $manager = $this->getDoctrine()->getManager();
        $repositoryUsers = $manager->getRepository('HncProjectBundle:User');
        $user = $repositoryUsers->findOneById($user_id);
        return $user;
    }

    public function user_settingsAction(Request $request)
    {
        $ftse_data = null;
        $user = new User();
        $manager = $this->getDoctrine()->getManager();
        $repositoryUsers = $manager->getRepository('HncProjectBundle:User');
        $user = $repositoryUsers->findOneById($this->get('session')->get('user_id'));

        $user_settingsFormBuider = $this->get('form.factory')->createBuilder(FormType::class, $user, ['allow_extra_fields' => true]);
        $user_settingsFormBuider
            ->add('firstname', TextType::class, ['label' => "Firstname :", 'attr' => ['class' => "form-control", 'value' => $user->getFirstname()]])
            ->add('lastname', TextType::class, ['label' => "Lastname :", 'attr' => ['class' => "form-control", 'value' => $user->getLastname()]])
            ->add('email', EmailType::class, ['label' => "Email :", 'attr' => ['class' => 'form-control', 'value' => $user->getEmail()]])
            ->add('birthday', BirthdayType::class, ['label' => "Birthday :", 'attr' => ['class' => 'form-control']])
            ->add('phonenumber', TelType::class, ['label' => "Phone number :", 'attr' => ['class' => 'form-control', 'value' => $user->getPhoneNumber()]])
            ->add('ChangeSettings', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'float : right']])
        ;
        $user_settingsForm = $user_settingsFormBuider->getForm();
        $user_settingsForm->handleRequest($request);

        if ($user_settingsForm->getClickedButton() && "ChangeSettings" === $user_settingsForm->getClickedButton()->getName()) {

            $newUserSettings = $user_settingsForm->getData();

            $entityManager = $this->getDoctrine()->getManager();

            $post = $this->getDoctrine()->getManager()->getRepository('HncProjectBundle:User')->findOneById($user->getId());

            if (!$post) {
                throw $this->createNotFoundException('Record not found...');
            }

            $firstname = $newUserSettings->getFirstname();
            $lastname = $newUserSettings->getLastname();
            $email = $newUserSettings->getEmail();
            $birthday = $newUserSettings->getBirthday();
            $phoneNumber = $newUserSettings->getPhoneNumber();

            $post->setFirstname($firstname);
            $post->setLastname($lastname);
            $post->setEmail($email);
            $post->setBirthday($birthday);
            $post->setPhonenumber($phoneNumber);

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('user_settings');

        }

        $user_passwordFormBuider = $this->get('form.factory')->createBuilder(FormType::class, $user, ['allow_extra_fields' => true]);
        $user_passwordFormBuider
            ->add('password', RepeatedType::class, ['type' => PasswordType::class,
                'first_options' => ['label'=> 'Password', 'attr' => ['class' => "form-control"]],
                'second_options' => ['label'=> 'Password confirmation', 'attr' => ['class' => "form-control"]]])
            ->add('ChangePassword', SubmitType::class, ['attr' => ['class' => 'btn btn-primary', 'style' => 'float : right']])
        ;
        $user_passwordForm = $user_passwordFormBuider->getForm();
        $user_passwordForm->handleRequest($request);

        if ($user_passwordForm->getClickedButton() && "ChangePassword" === $user_passwordForm->getClickedButton()->getName()) {

            echo 'mdrrrr';
        }

        return $this->render('@HncProject\User\user_settings.html.twig', ['ftse_data' => $ftse_data, 'logged_in' => false, 'user_settingsForm' => $user_settingsForm->createView(), 'user_passwordForm' => $user_passwordForm->createView()]);
    }
}