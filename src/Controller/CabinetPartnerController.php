<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CabinetPartnerController
 * @package App\Controller
 */
final class CabinetPartnerController extends AbstractController
{
    /**
     * @Route("/cabinet/partner", name="partner_profile")
     * @param TransactionRepository $transactionRepository
     * @return Response
     */
    public function cabinetProfile(TransactionRepository $transactionRepository): Response
    {
        $partner = $this->getUser()->getPartner();
        $clients = $partner->getClients();
        $transactions = [];
        foreach ($clients as $client) {
            $transactions = array_merge($transactions, $transactionRepository->findBy(['sender' => $client, 'partner' => null]));
        }

        return $this->render('cabinet/partner/index.html.twig', [
            'transactions' => $transactions,
            'clients' => $clients,
            'page' => 'clients'
        ]);
    }

    /**
     * @Route("/cabinet/partner/stats", name="partner_stats")
     * @param TransactionRepository $transactionRepository
     * @return Response
     */
    public function cabinetClients(TransactionRepository $transactionRepository): Response
    {
        $partner = $this->getUser()->getPartner();
        $clients = $partner->getClients();
        $transactions = $transactionRepository->findBy(['partner' => $partner]);

        return $this->render('cabinet/partner/index.html.twig', [
            'transactions' => $transactions,
            'clients' => $clients,
            'page' => 'stats'
        ]);
    }
}
