<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickDetailsController extends AbstractController
{
    /**
     * @Route("/trick/details/{slug}", name="trick_details")
     */
    public function index(TrickRepository $repo, $slug)
    {
        $trick = $repo->findOneBySlug($slug);

        return $this->render('trick_details/index.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * Get the 5 next comments in the database and create a Twig file with them that will be displayed via Javascript
     * 
     * @Route("/trick/{slug}/{start}", name="loadMoreComments", requirements={"start": "\d+"})
     */
    public function loadMoreComments(TrickRepository $repo, $slug, $start = 5)
    {
        $trick = $repo->findOneBySlug($slug);

        return $this->render('trick_details/loadMoreComments.html.twig', [
            'trick' => $trick,
            'start' => $start
        ]);
    }
}
