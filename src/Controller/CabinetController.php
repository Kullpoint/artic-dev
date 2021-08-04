<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Form\AvatarType;
use App\Form\ChangeDataFormType;
use App\Form\ChangeOldPasswordFormType;
use App\Service\FileUploader;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CabinetController
 * @package App\Controller
 */
final class CabinetController extends AbstractController
{
    /**
     * @Route("/cabinet/settings", name="user_settings")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param FileUploader $fileUploader
     * @return Response
     * @throws Exception
     */
    public function cabinetSettings(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        FileUploader $fileUploader
    ): Response
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(AvatarType::class);
        $form->handleRequest($request);

        if ($user->getPerformer()) {
            if ($form->isSubmitted()) {
                $avatar = $form->get('avatar')->getData();
                $filename = $fileUploader->upload($avatar, $this->getParameter('avatars_directory'));
                $performer = $user->getPerformer();

                $performer->setAvatar($filename);
                $em->persist($performer);
                $em->flush();
            }
        }

        $changeDataForm = $this->createForm(ChangeDataFormType::class, [
            'user' => $user,
        ]);
        $changeDataForm->handleRequest($request);

        if ($changeDataForm->isSubmitted() && $changeDataForm->isValid()) {
            $user->setFirstName($changeDataForm->get('firstname')->getData());
            $user->setLastName($changeDataForm->get('lastname')->getData());
            $user->setPhone($changeDataForm->get('phone')->getData());
            $user->setEmail($changeDataForm->get('email')->getData());
            if ($user->getClient()) {
                $user->getClient()->setCountry($changeDataForm->get('country')->getData());
                $user->getClient()->setCity($changeDataForm->get('city')->getData());
            } else if ($user->getPerformer()) {
                $user->getPerformer()->setBirthday(new DateTime($changeDataForm->get('birthday')->getData()));
                $user->getPerformer()->setCountry($changeDataForm->get('country')->getData());
                $user->getPerformer()->setCity($changeDataForm->get('city')->getData());
            } else if ($user->getPartner()) {
                $user->getPartner()->setBirthday(new DateTime($changeDataForm->get('birthday')->getData()));
            }

            $em->persist($user);
            $em->flush();
        }

        $changePassForm = $this->createForm(ChangeOldPasswordFormType::class);
        $changePassForm->handleRequest($request);

        if ($changePassForm->isSubmitted()) {
            $checkOldPass = $passwordEncoder->isPasswordValid(
                $user,
                $changePassForm->get('oldPassword')->getData()
            );

            if ($checkOldPass === false) {
                $changePassForm->get('oldPassword')->addError(new FormError('Wrong old password'));
            }

            if ($changePassForm->get('password')->get('first')->getData() != $changePassForm->get('password')->get('second')->getData()) {
                $changePassForm->get('password')->addError(new FormError('New passwords are not equal'));
            }

            if ($changePassForm->isValid()) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $changePassForm->get('password')->getData()
                    )
                );

                $em->persist($user);
                $em->flush();
            }
        }

        return $this->render('cabinet/settings.html.twig', [
            'form' => $form->createView(),
            'changePassForm' => $changePassForm->createView(),
            'changeDataForm' => $changeDataForm->createView(),
        ]);
    }

    /**
     * @Route("/cabinet/order/{id}", name="order")
     * @param Order $order
     * @return Response
     */
    public function doneOrder(Order $order): Response
    {
        $comments = $order->getOrderComments();

        if (count($comments) == 0) {
            $comments = null;
        }

        return $this->render('cabinet/order.html.twig', [
            'order' => $order,
            'comments' => $comments
        ]);
    }
}
