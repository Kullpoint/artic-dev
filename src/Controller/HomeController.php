<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Performer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller
 */
final class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $allCategories = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        $categories = array_slice($allCategories, 0, 6);

        $allPerformers = $this
            ->getDoctrine()
            ->getRepository(Performer::class)
            ->findBy([], ['rating' => 'DESC']);
        $performers = array_slice($allPerformers, 0, 16);

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'allCategories' => $allCategories,
            'performers' => $performers,
        ]);
    }

    /**
     * @Route("/categories", name="categories")
     */
    public function categories(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('home/categories.html.twig', [
            'categories' => $categories,
        ]);
    }
}
