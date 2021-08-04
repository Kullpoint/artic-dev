<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Transaction;
use App\Form\PaymentType;
use App\Repository\OrderRepository;
use App\Service\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CabinetUserController
 * @package App\Controller
 */
final class CabinetUserController extends AbstractController
{
    /**
     * @Route("/cabinet/user", name="user_profile")
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     */
    public function cabinetProfile(OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();

        if ($request->query->get('filter1')) {
            $orders = $orderRepository->findBy(['client' => $user->getClient(), 'status' => ['new', 'confirmed', 'in process', 'ready']], [$request->query->get('filter1') => 'DESC']);
        } else {
            $orders = $orderRepository->findBy(['client' => $user->getClient(), 'status' => ['new', 'confirmed', 'in process', 'ready']]);
        }

        if ($request->query->get('filter2')) {
            $readyOrders = $orderRepository->findBy(['client' => $user->getClient(), 'status' => ['done']], [$request->query->get('filter2') => 'DESC']);
        } else {
            $readyOrders = $orderRepository->findBy(['client' => $user->getClient(), 'status' => ['done']]);
        }

        return $this->render('cabinet/user/index.html.twig', [
            'orders' => $orders,
            'readyOrders' => $readyOrders
        ]);
    }

    /**
     * @Route("/cabinet/user/orderRequest/{id}", name="user_order_request")
     * @param $id
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function cabinetOrderRequest($id, OrderRepository $orderRepository): Response
    {
        $orderRequest = $orderRepository->find($id);

        return $this->render('cabinet/user/orderRequest.html.twig', [
            'order' => $orderRequest,
        ]);
    }

    /**
     * @Route("/cabinet/orderRequest/{id}/confirm", name="user_orderRequest_confirm")
     * @param $id
     * @param OrderRepository $orderRepository
     * @param Request $request
     * @return Response
     * @throws ApiErrorException
     */
    public function cabinetOrderRequestConfirm($id, OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user->getClient()) {
            return $this->redirectToRoute('home');
        }
        $em = $this->getDoctrine()->getManager();

        $order = $orderRepository->find($id);

        $form = $this->createForm(PaymentType::class, [
            'amount' => $order->getPrice(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $performer = $order->getPerformer();
            $order->setStatus('in process');
            $em->persist($order);

            $balance = $performer->getPendingBalance();
            $performerMoney = (int)$order->getPrice() * 0.95;
            $pendingBalance = $balance + $performerMoney;
            $performer->setPendingBalance($pendingBalance);
            $em->persist($user);
            $em->flush();

            Stripe::setApiKey($this->getParameter('stripe_secret_key'));
            $charge = \Stripe\Charge::create([
                'amount' => $form->get('amount')->getData() * 100,
                'currency' => $this->getParameter('payment')['currency'],
                'description' => 'Premium blog access',
                'source' => $form->get('token')->getData(),
                'receipt_email' => $user->getEmail(),
            ]);

            $client = $order->getClient();

            $transaction = new Transaction();
            $transaction->setStatus('pending');
            $transaction->setReceiver($order->getPerformer());
            $transaction->setSender($client);
            $transaction->setAmount($performerMoney);
            $transaction->setCommission((int)$order->getPrice() - (int)$performerMoney);
            $transaction->setChargeId($charge->id);
            $transaction->setPaid($charge->paid);
            $transaction->setOrder($order);
            $em->persist($transaction);
            $em->flush();

            if ($client->getPartner()) {
                if (count($client->getTransactions()) === 0) {
                    $percentage = 0.8;
                } else {
                    $percentage = 0.5;
                }

                $partnerMoney = (int)$order->getPrice() * 0.05 * $percentage;

                $transaction = new Transaction();
                $transaction->setStatus('pending');
                $transaction->setPartner($client->getPartner());
                $transaction->setSender($client);
                $transaction->setAmount($partnerMoney);
                $transaction->setCommission(0);
                $transaction->setChargeId($charge->id);
                $transaction->setPaid($charge->paid);
                $transaction->setOrder($order);
                $em->persist($transaction);
                $em->flush();
            }

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('cabinet/user/orderRequest.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    /**
     * @Route("/cabinet/user/order/{id}", name="user_order")
     * @param $id
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function cabinetOrder($id, OrderRepository $orderRepository)
    {
        $order = $orderRepository->find($id);
        $comments = $order->getOrderComments();

        return $this->render('cabinet/user/order.html.twig', [
            'order' => $order,
            'comments' => $comments
        ]);
    }

    /**
     * @Route("/cabinet/user/order/{id}/confirm", name="order_confirm")
     * @param Order $order
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function cabinetReadyOrderConfirm(Order $order, EntityManagerInterface $em): Response
    {
        $order = TransactionService::confirmOrder($order, $em);

        return $this->redirectToRoute('order', [
            'id' => $order->getId(),
        ]);
    }

    /**
     * @Route("/cabinet/user/order/{id}/decline", name="order_not_ready")
     * @param Order $order
     * @return Response
     */
    public function cabinetNotReadyOrder(Order $order): Response
    {
        $order->setStatus('in process');
        $order->setDoc('');

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('user_order', [
            'id' => $order->getId(),
        ]);
    }
}
