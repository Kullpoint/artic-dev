<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\TransactionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/transaction")
 */
final class TransactionController extends AbstractController
{
    /**
     * @Route("/", name="admin_transactions")
     * @param Request $request
     * @param TransactionRepository $transactionRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function clients(Request $request, TransactionRepository $transactionRepository, PaginatorInterface $paginator)
    {
        $transactions = $transactionRepository->findAll();

        $pagination = $paginator->paginate(
            $transactions,
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/transaction/index.html.twig', [
            'transactions' => $pagination,
        ]);
    }

    /**
     * @Route("/go", name="admin_transactionsgo")
     * @param Request $request
     * @return Response
     */
    public function go(Request $request)
    {
        $page = $request->get('page');

        return $this->redirectToRoute('admin_transactions', [
            'page' => $page,
        ]);
    }
}
