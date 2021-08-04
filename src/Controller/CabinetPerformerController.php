<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Form\ConfirmOrderRequestType;
use App\Form\OrderReadyType;
use App\Repository\OrderRepository;
use App\Repository\TransactionRepository;
use App\Service\FileUploader;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CabinetPerformerController
 * @package App\Controller
 * @Route("/cabinet/performer")
 */
final class CabinetPerformerController extends AbstractController
{
    /**
     * @Route("/", name="performer_profile")
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     */
    public function cabinetProfile(OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();

        if ($request->query->get('filter1')) {
            $orders = $orderRepository->findBy(['performer' => $user->getPerformer(), 'status' => ['new', 'confirmed', 'in process', 'ready']], [$request->query->get('filter1') => 'DESC']);
        } else {
            $orders = $orderRepository->findBy(['performer' => $user->getPerformer(), 'status' => ['new', 'confirmed', 'in process', 'ready']]);
        }

        if ($request->query->get('filter2')) {
            $readyOrders = $orderRepository->findBy(['performer' => $user->getPerformer(), 'status' => ['done']], [$request->query->get('filter2') => 'DESC']);
        } else {
            $readyOrders = $orderRepository->findBy(['performer' => $user->getPerformer(), 'status' => ['done']]);
        }

        return $this->render('cabinet/performer/index.html.twig', [
            'orders' => $orders,
            'readyOrders' => $readyOrders
        ]);
    }

    /**
     * @Route("/orderRequest/{id}/confirm", name="performer_order_confirm")
     * @param Order $order
     * @param Request $request
     * @return Response
     */
    public function cabinetOrderRequestConfirm(
        Order $order,
        Request $request
    ): Response
    {
        if ($order->getStatus() !== 'new') {
            return $this->redirectToRoute('performer_profile');
        }

        $form = $this->createForm(ConfirmOrderRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $price = explode('-', $order->getPrice());
            $priceFrom = $price[0];
            $priceTo = $price[1];
            if ((int)$priceFrom > (int)$form->get('price')->getData()) {
                $form->get('price')->addError(new FormError('Should be in range ' . $order->getPrice()));
            }
            if ((int)$priceTo < (int)$form->get('price')->getData()) {
                $form->get('price')->addError(new FormError('Should be in range ' . $order->getPrice()));
            }

            if ($form->isValid()) {
                $order->setStatus('confirmed');
                $order->setPrice($form->get('price')->getData());
                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();

                return $this->redirectToRoute('performer_profile');
            }
        }

        return $this->render('cabinet/performer/orderRequest.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/orderRequest/{id}", name="performer_order_request")
     * @param Order $order
     * @return Response
     */
    public function cabinetOrderRequest(Order $order): Response
    {
        return $this->render('cabinet/performer/orderRequest.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/order/{id}", name="performer_order")
     * @param Order $order
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return Response
     * @throws Exception
     */
    public function cabinetClientOrder(
        Order $order,
        Request $request,
        FileUploader $fileUploader
    ): Response
    {
        $comments = $order->getOrderComments();

        $form = $this->createForm(OrderReadyType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $doc = $form->get('doc')->getData();

            if ($doc) {
                $newFilename = $fileUploader->upload($doc, $this->getParameter('docs_directory'));

                $order->setDoc($newFilename);
                $order->setStatus('ready');

                $em = $this->getDoctrine()->getManager();
                $em->persist($order);
                $em->flush();
            }

            return $this->redirectToRoute('performer_profile');
        }

        return $this->render('cabinet/performer/order.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/transactions", name="performer_transactions")
     * @param TransactionRepository $transactionRepository
     * @return Response
     */
    public function cabinetClients(TransactionRepository $transactionRepository): Response
    {
        $performer = $this->getUser()->getPerformer();
        $transactions = $transactionRepository->findBy(['receiver' => $performer]);

        return $this->render('cabinet/performer/transactions.html.twig', [
            'transactions' => $transactions,
        ]);
    }
}
