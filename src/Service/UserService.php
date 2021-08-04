<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserService
 * @package App\Service
 */
class UserService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     */
    public function userAction(Request $request)
    {
        if ($request->query->get('block')) {
            $user = $this->userRepository->find($request->query->get('user'));
            $this->blockUser($user);
        } elseif ($request->query->get('remove')) {
            $user = $this->userRepository->find($request->query->get('user'));
            $this->removeUser($user);
        }
    }

    /**
     * @param User $user
     */
    public function blockUser(User $user)
    {
        if ($user->isBlocked()) {
            $user->setIsBlocked(false);
        } else {
            $user->setIsBlocked(true);
        }
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        if ($user->getPartner()) {
            $this->em->remove($user->getPartner());
        } elseif ($user->getPerformer()) {
            $this->em->remove($user->getPerformer());
        } elseif ($user->getClient()) {
            $this->em->remove($user->getClient());
        }
        $this->em->remove($user);
        $this->em->flush();
    }
}