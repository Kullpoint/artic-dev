<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Partner;
use App\Entity\Performer;
use App\Entity\User;
use App\Entity\Client;
use App\Form\RegistrationPartnerFormType;
use App\Form\RegistrationPerformerFormType;
use App\Form\RegistrationUserFormType;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use App\Service\FileUploader;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Class RegistrationController
 * @package App\Controller
 */
final class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @param $length
     * @return string
     */
    private function generate_code($length): string
    {
        $data[] = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 'a', 'b', 'c', 'd', 'e', 'f');
        $res = '';
        for ($i = 0; $i < $length; $i++) {
            $res .= $data[rand(0, count($data))];
        }
        return $res;
    }

    /**
     * @Route("/register/role", name="app_register")
     * @return Response
     * @throws Exception
     */
    public function register(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('registration/registerRole.html.twig');
    }

    /**
     * @Route("/register/user", name="registerUser")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param SluggerInterface $slugger
     * @return Response
     * @throws Exception
     */
    public function registerUser(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->query->get('referral')) {
            $request->cookies->set('referral', $request->query->get('referral'));
        }

        $form = $this->createForm(RegistrationUserFormType::class, [
            'user' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->verify($form);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $user = $this->newUser($passwordEncoder, $form);

                $client = new Client();
                $user->setRoles(['ROLE_USER']);
                $user->setClient($client);
                if ($request->cookies->get('referral')) {
                    /** @var Partner $partner */
                    $partner = $this
                        ->getDoctrine()
                        ->getRepository(Partner::class)
                        ->findOneBy([
                            'referral' => $request->cookies->get('referral')
                        ]);
                    $user->setPartner($partner);
                }
                $client->setCountry($form->get('country')->getData());
                $client->setCity($form->get('city')->getData());

                $entityManager->persist($client);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->sendEmailConfirmation($user);

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render('registration/registerUser.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/performer/step/1", name="registerPerformer")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function registerPerformerStep1(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(RegistrationUserFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->verify($form);

            if ($form->isValid()) {
                $data = $form->getData();
                setcookie('password', $form->get('password')->getData());
                foreach ($data as $key => $value) {
                    setcookie($key, $value);
                }

                return $this->redirectToRoute('registerPerformerStep2');
            }
        }

        return $this->render('registration/registerPerformerStep1.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/performer/step/2", name="registerPerformerStep2")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param FileUploader $fileUploader
     * @return Response
     * @throws Exception
     */
    public function registerPerformerStep2(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        FileUploader $fileUploader
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        } elseif (
            empty($_COOKIE['password'])
            && empty($_COOKIE['lastname'])
            && empty($_COOKIE['firstname'])
        ) {
            return $this->redirectToRoute('registerPerformer');
        }

        $form = $this->createForm(RegistrationPerformerFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()
//            && $form->isValid()
        ) {
            $entityManager = $this->getDoctrine()->getManager();

            $user = new User();
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $_COOKIE['password']
                )
            );
            $user->setEmail($_COOKIE['email']);
            $user->setPhone($_COOKIE['phone']);
            $user->setLastName($_COOKIE['lastname']);
            $user->setFirstName($_COOKIE['firstname']);
            $user->setSendEmail($form->get('sendEmail')->getData());
            $user->setIsVerified(false);

            $user->setRoles(['ROLE_PERFORMER']);
            $performer = new Performer();
            $avatar = $form->get('avatar')->getData();
            $ava = $fileUploader->upload($avatar, $this->getParameter('avatars_directory'));
            $performer->setAvatar($ava);
            $performer->setBirthday($form->get('birthday')->getData());
            $performer->setCountry($_COOKIE['country']);
            $performer->setCity($_COOKIE['city']);
            $specializations = $form->get('specialization')->getData();
            foreach ($specializations as $specialization) {
                $performer->addCategory($specialization);
            }
            $performer->setExperience($form->get('experience')->getData());
            $summeryTypes = implode(', ', $form->get('summeryTypes')->getData());
            $performer->setSummeryTypes($summeryTypes);
            $user->setPerformer($performer);

            $entityManager->persist($performer);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->sendEmailConfirmation($user);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/registerPerformerStep2.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/partner", name="registerPartner")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @return Response
     * @throws Exception
     */
    public function registerPartner(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(RegistrationPartnerFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->verify($form);

            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $user = $this->newUser($passwordEncoder, $form);

                $user->setRoles(['ROLE_PARTNER']);
                $partner = new Partner();
                $referralCode = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
                $referralLink = $referralCode;
                $partner->setReferral($referralLink);
                $partner->setBirthday($form->get('birthday')->getData());
                $user->setPartner($partner);

                $entityManager->persist($partner);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->sendEmailConfirmation($user);

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render('registration/registerPartner.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function verify($form)
    {
        if ($form->get('password')->get('first')->getData() != $form->get('password')->get('second')->getData()) {
            $form->get('password')->addError(new FormError('Passwords are not equal'));
        }

        $email = $form->get('email')->getData();
        $phone = $form->get('phone')->getData();
        $userEmailExist = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
        $userPhoneExist = $this->getDoctrine()->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if ($userEmailExist) {
            $form->get('email')->addError(new FormError('That email already exist'));
        }

        if ($userPhoneExist) {
            $form->get('phone')->addError(new FormError('That phone already exist'));
        }
    }

    private function newUser($passwordEncoder, $form): User
    {
        $user = new User();
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('password')->getData()
            )
        );
        $user->setEmail($form->get('email')->getData());
        $user->setPhone($form->get('phone')->getData());
        $user->setLastName($form->get('lastname')->getData());
        $user->setFirstName($form->get('firstname')->getData());
        $user->setSendEmail($form->get('sendEmail')->getData());
        $user->setIsVerified(false);

        return $user;
    }

    private function sendEmailConfirmation($user)
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from('Artic')
                ->sender('yura.sobchak@gmail.com')
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     * @param Request $request
     * @return Response
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $user = $this->getUser();
            $user->setIsVerified(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('home');
    }
}
