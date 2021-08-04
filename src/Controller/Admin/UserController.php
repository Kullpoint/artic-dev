<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\ClientRepository;
use App\Repository\PartnerRepository;
use App\Repository\PerformerRepository;
use App\Service\UserService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user")
 */
final class UserController extends AbstractController
{
    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/clients", name="admin_users")
     * @param Request $request
     * @param ClientRepository $clientRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function clients(
        Request $request,
        ClientRepository $clientRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $this->userService->userAction($request);

        $users = $clientRepository->findAll();

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)
        );

        if ($request->query->get('ajax')) {
            return $this->render('admin/ajax/user.html.twig', [
                'users' => $pagination,
            ]);
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/performers", name="admin_performers")
     * @param Request $request
     * @param PerformerRepository $performerRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function performers(
        Request $request,
        PerformerRepository $performerRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $this->userService->userAction($request);

        $users = $performerRepository->findAll();

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)
        );

        if ($request->query->get('ajax')) {
            return $this->render('admin/ajax/user.html.twig', [
                'users' => $pagination,
            ]);
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/partners", name="admin_partners")
     * @param Request $request
     * @param PartnerRepository $partnerRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function partners(
        Request $request,
        PartnerRepository $partnerRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $this->userService->userAction($request);

        $users = $partnerRepository->findAll();

        $pagination = $paginator->paginate(
            $users,
            $request->query->getInt('page', 1)
        );

        if ($request->query->get('ajax')) {
            return $this->render('admin/ajax/user.html.twig', [
                'users' => $pagination,
            ]);
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $pagination,
        ]);
    }

    /**
     * @Route("/clients/go", name="admin_usersgo")
     * @param Request $request
     * @return Response
     */
    public function go(Request $request): Response
    {
        $page = $request->get('page');

        return $this->redirectToRoute('admin_users', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/partners/go", name="admin_partnersgo")
     * @param Request $request
     * @return Response
     */
    public function partnersGo(Request $request): Response
    {
        $page = $request->get('page');

        return $this->redirectToRoute('admin_partners', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/performers/go", name="admin_performersgo")
     * @param Request $request
     * @return Response
     */
    public function performersGo(Request $request): Response
    {
        $page = $request->get('page');

        return $this->redirectToRoute('admin_performers', [
            'page' => $page,
        ]);
    }
}
