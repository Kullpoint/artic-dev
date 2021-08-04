<?php


namespace App\Service;


use App\Entity\Order;
use App\Helper\PerformerHelper;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    public static function confirmOrder(Order $order, EntityManagerInterface $em): Order
    {
        $performer = $order->getPerformer();

        $order->setStatus('done');
        $price = $order->getPrice();
        $performerPrice = (int)$price * 0.95;
        $pendingBalance = (int)$performer->getPendingBalance() - $performerPrice;
        $balance = (int)$performer->getBalance() + $performerPrice;
        $performer->setPendingBalance($pendingBalance);
        $performer->setBalance($balance);

        $em->persist($order);
        $em->persist($performer);
        $em->flush();

        $performer = $order->getPerformer();
        $rating = PerformerHelper::performerRating($performer);
        $performer->setRating($rating);
        $em->persist($performer);

        $transactions = $order->getTransactions();
        foreach ($transactions as $transaction) {
            $transaction->setStatus('success');
            $partner = $order->getClient()->getPartner();
            if ($partner === $transaction->getPartner()) {
                $partner->setBalance($partner->getBalance() + $transaction->getAmount() * 0.5);
            }
            $em->persist($transaction);
        }
        $em->flush();

        return $order;
    }
}