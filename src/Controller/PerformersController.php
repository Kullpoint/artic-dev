<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Complaint;
use App\Entity\Order;
use App\Entity\Performer;
use App\Entity\Review;
use App\Form\FilterPerformersType;
use App\Form\OrderSummeryType;
use App\Form\ReviewType;
use App\Repository\PerformerRepository;
use DateTime;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class PerformersController
 * @package App\Controller
 */
final class PerformersController extends AbstractController
{
    /**
     * @Route("/performers", name="performers")
     * @param PerformerRepository $performersRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PerformerRepository $performersRepository, PaginatorInterface $paginator, Request $request)
    {
        if (!empty($request->query->get('category')) && !empty($request->query->get('performer'))) {
            $performers = $performersRepository->searchPerformers([
                'category' => $request->query->get('category'),
                'performer' => $request->query->get('performer')
            ]);
        } elseif (!empty($request->query->get('performer'))) {
            $performers = $performersRepository->searchPerformers(['performer' => $request->query->get('performer')]);
        } elseif (!empty($request->query->get('category'))) {
            $performers = $performersRepository->searchPerformers(['category' => $request->query->get('category')]);
        } else {
            $performers = $performersRepository->findAll();
        }

        $pagination = $paginator->paginate(
            $performers,
            $request->query->getInt('page', 1),
            5
        );
        if ($request->get('category')) {
            $category = $this->getDoctrine()->getRepository(Category::class)->find($request->get('category'));
        }
        $filterForm = $this->createForm(FilterPerformersType::class, [
            'category' => $category ?? null
        ]);

        return $this->render('performers/index.html.twig', [
            'performers' => $pagination,
            'total' => count($performers),
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/performers/go", name="performergo")
     * @param Request $request
     * @return Response
     */
    public function go(Request $request)
    {
        $page = $request->get('page');

        return $this->redirectToRoute('performers', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/filterPerformers", name="filterPerformers")
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function filterPerformers(Request $request, PaginatorInterface $paginator)
    {
        $performersRepository = $this->getDoctrine()->getRepository(Performer::class);
        $filteredPerformers = $performersRepository->filterPerformers($request->request);
        $pagination = $paginator->paginate(
            $filteredPerformers,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('ajax/filteredPerformers.html.twig', [
            'performers' => $pagination,
            'total' => count($filteredPerformers),
            'sortBy' => $request->request->get('sortBy'),
        ]);
    }

    /**
     * @Route("/searchPerformers", name="searchPerformers")
     * @param Request $request
     * @return Response
     */
    public function searchPerformers(Request $request): Response
    {
        $category = $request->request->get('category');
        return $this->redirectToRoute('performers', [
            'category' => $category,
            'performer' => $request->request->get('performer')
        ]);
    }

    /**
     * @Route("/performer/{id}", name="performer")
     * @param $id
     * @param PerformerRepository $performersRepository
     * @param Request $request
     * @return Response
     */
    public function performer($id, PerformerRepository $performersRepository, Request $request)
    {
        if ($this->getUser() && $this->getUser()->getPerformer()) {
            return $this->redirectToRoute('home');
        }
        $performer = $performersRepository->findOneBy(['id' => $id]);
        $summeryTypes = explode(', ', $performer->getSummeryTypes());
        if ($this->getUser()) {
            $order = $this
                ->getDoctrine()
                ->getRepository(Order::class)
                ->findOneBy(['client' => $this->getUser()->getClient(), 'performer' => $performer, 'status' => 'ready']);

            $orderForm = $this->createForm(OrderSummeryType::class, [
                'categories' => $performer->getCategories()
            ]);
            $orderForm->handleRequest($request);
            if ($orderForm->isSubmitted()) {
                $orderRequest = new Order();
                $orderRequest->setPerformer($performer);
                $orderRequest->setClient($this->getUser()->getClient());
                $orderRequest->setTheme($orderForm->get('theme')->getData());
                $orderRequest->setCategory($orderForm->get('category')->getData());
                $orderRequest->setSummeryType($orderForm->get('summeryType')->getData());
                $orderRequest->setDescription($orderForm->get('description')->getData());
                $orderRequest->setRequirements($orderForm->get('requirements')->getData());
                $orderRequest->setPrice($orderForm->get('priceFrom')->getData() . '-' . $orderForm->get('priceTo')->getData());
                $orderRequest->setDeadline($orderForm->get('deadline')->getData());
                $orderRequest->setStatus('new');

                $em = $this->getDoctrine()->getManager();
                $em->persist($orderRequest);
                $em->flush();

                return $this->redirectToRoute('performers');
            }

            return $this->render('performers/performer.html.twig', [
                'performer' => $performer,
                'summeryTypes' => $summeryTypes,
                'orderForm' => $orderForm->createView(),
                'order' => $order,
            ]);
        }

        return $this->render('performers/performer.html.twig', [
            'performer' => $performer,
            'summeryTypes' => $summeryTypes,
        ]);
    }

    /**
     * @Route("/performer/{id}/reviews", name="performerReviews")
     * @param Performer $performer
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function performerReviews(Performer $performer, Request $request)
    {
        $client = $this->getUser()->getClient();
        if ($request->query->get('order')) {
            $order = $order = $this
                ->getDoctrine()
                ->getRepository(Order::class)
                ->find($request->query->get('order'));
        } else {
            $order = $this
                ->getDoctrine()
                ->getRepository(Order::class)
                ->findOneBy(['client' => $client, 'performer' => $performer, 'status' => 'done']);
        }
        $summeryTypes = explode(', ', $performer->getSummeryTypes());

        if ($request->query->get('filter')) {
            $reviews = $this
                ->getDoctrine()
                ->getRepository(Review::class)
                ->findBy(['performer' => $performer], [$request->query->get('filter') => 'DESC']);
        } else {
            $reviews = $this
                ->getDoctrine()
                ->getRepository(Review::class)
                ->findBy(['performer' => $performer]);
        }

        $reviewForm = $this->createForm(ReviewType::class);
        $reviewForm->handleRequest($request);

        if ($reviewForm->isSubmitted()) {
            $review = new Review();
            $review->setPerformer($performer);
            $review->setClient($client);
            $review->setReviewedOrder($order);
            $review->setMark($request->request->get('mark'));
            $review->setText($reviewForm->get('text')->getData());
            $review->setDate(new DateTime());

            $performer = $order->getPerformer();
            $reviews = $performer->getReviews();
            $marks = [];
            foreach ($reviews as $oldReview) {
                $mark = $oldReview->getMark();
                $marks[] = (int)$mark;
            }
            $rating = array_sum($marks) / count($marks);
            if (is_float($rating)) {
                $rating = bcdiv($rating, 1, 2);
            }
            $performer->setRating($rating);

            $em = $this->getDoctrine()->getManager();
            $em->persist($performer);
            $em->persist($review);
            $em->flush();

            if ($reviewForm->get('type')->getData() === 'complaint') {
                $complaint = new Complaint();
                $complaint->setPerformer($performer);
                $complaint->setClient($client);
                $complaint->setReview($review);
                $complaint->setText($reviewForm->get('text')->getData());
                $review->setComplaint($complaint);

                $em->persist($complaint);
            }

            $em->flush();

            return $this->redirectToRoute('performerReviews', ['id' => $performer->getId()]);
        }

        return $this->render('performers/reviews.html.twig', [
            'performer' => $performer,
            'summeryTypes' => $summeryTypes,
            'reviewForm' => $reviewForm->createView(),
            'order' => $order,
            'reviews' => $reviews
        ]);
    }
}
