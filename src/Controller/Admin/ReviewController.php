<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Complaint;
use App\Repository\ComplaintRepository;
use App\Repository\ReviewRepository;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/review")
 */
final class ReviewController extends AbstractController
{
    /**
     * @Route("/", name="admin_reviews")
     * @param Request $request
     * @param ReviewRepository $reviewRepository
     * @param ComplaintRepository $complaintRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function reviews(
        Request $request,
        ReviewRepository $reviewRepository,
        ComplaintRepository $complaintRepository,
        PaginatorInterface $paginator
    ): Response
    {
        $reviews = $reviewRepository->findAll();
        $complaints = $complaintRepository->findAll();

        $reviewsPagination = $paginator->paginate(
            $reviews,
            $request->query->getInt('page', 1)
        );

        $complaintsPagination = $paginator->paginate(
            $complaints,
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/review/index.html.twig', [
            'reviews' => $reviewsPagination,
            'complaints' => $complaintsPagination,
        ]);
    }

    /**
     * @Route("/reviews/go", name="admin_reviewsgo")
     * @param Request $request
     * @return Response
     */
    public function reviewsGo(Request $request): Response
    {
        $page = $request->get('page');

        return $this->redirectToRoute('admin_reviews', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/complaints/go", name="admin_complaintsgo")
     * @param Request $request
     * @return Response
     */
    public function complaintsGo(Request $request): Response
    {
        $page = $request->get('page');

        return $this->redirectToRoute('admin_reviews', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/complaint/{id}/confirm", name="complaint_confirm")
     * @param Complaint $complaint
     * @return Response
     */
    public function complaintConfirm(Complaint $complaint): Response
    {
        $complaint->setStatus('confirmed');
        $performer = $complaint->getPerformer();
        $order = $complaint->getReview()->getReviewedOrder();
        $performer->setPendingBalance($performer->getPendingBalance() - $order->getPrice());
        // TODO: stripe refund payment, block performer. persist flush

        return $this->redirectToRoute('admin_reviews');
    }

    /**
     * @Route("/complaint/{id}/decline", name="complaint_decline")
     * @param Complaint $complaint
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function complaintDecline(Complaint $complaint, EntityManagerInterface $em): Response
    {
        $order = $complaint->getReview()->getReviewedOrder();
        $complaint->setStatus('declined');
        TransactionService::confirmOrder($order, $em);

        // TODO: stripe confirm payment. persist flush

        return $this->redirectToRoute('admin_reviews');
    }
}