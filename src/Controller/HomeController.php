<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Display the home page
     * 
     * @Route("/", name="home")
     */
    public function index(TrickRepository $repo)
    {
        // Get 15 tricks from position 0
        $tricks = $repo->findBy([], [], 15, 0);

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks
        ]);
    }
    
    /**
     * Get the 15 next tricks in the database and create a Twig file with them that will be displayed via Javascript
     * 
     * @Route("/{start}", name="loadMoreTricks", requirements={"start": "\d+"})
     */
    public function loadMoreTricks(TrickRepository $repo, $start = 15)
    {
        // Get 15 tricks from the start position
        $tricks = $repo->findBy([], [], 15, $start);

        return $this->render('home/loadMoreTricks.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
