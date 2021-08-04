<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\OrderComment;
use App\Entity\User;
use App\Repository\OrderCommentRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CabinetActionsController extends AbstractController
{
    private function requestsRoute(): string
    {
        $route = null;
        $user = $this->getUser();
        if ($user->getClient()) {
            $route = 'user_profile';
        } elseif ($user->getPerformer()) {
            $route = 'performer_profile';
        }
        return $route;
    }

    /**
     * @Route("/cabinet/order/reject/{id}", name="order_reject")
     * @param $id
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function cabinetOrderReject($id, OrderRepository $orderRepository): Response
    {
        $route = $this->requestsRoute();

        $orderRequest = $orderRepository->find($id);
        $orderRequest->setStatus('declined');

        $em = $this->getDoctrine()->getManager();
        $em->persist($orderRequest);
        $em->flush();

        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/cabinet/order/delete/{id}", name="order_delete")
     * @param $id
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function cabinetOrderDelete($id, OrderRepository $orderRepository): Response
    {
        $route = $this->requestsRoute();

        $orderRequest = $orderRepository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($orderRequest);
        $em->flush();

        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/order/addComment", name="order_comment")
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function cabinetOrderAddComment(Request $request, OrderRepository $orderRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $order = $orderRepository->find($request->request->get('id'));

        $orderComment = new OrderComment();
        $orderComment->setOrder($order);
        $orderComment->setUser($user);
        $orderComment->setComment($request->request->get('comment'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($orderComment);
        $em->flush();

        $comments = $order->getOrderComments();

        return $this->render('ajax/comments.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/order/deleteComment", name="delete_comment")
     * @param Request $request
     * @param OrderCommentRepository $orderCommentsRepository
     * @return Response
     */
    public function cabinetOrderDeleteComment(
        Request $request,
        OrderCommentRepository $orderCommentsRepository
    ): Response
    {
        $orderComment = $orderCommentsRepository->find($request->request->get('id'));
        $em = $this->getDoctrine()->getManager();
        $em->remove($orderComment);
        $em->flush();

        return new Response('success', 200);
    }
}
