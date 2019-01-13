<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickDetailsController extends AbstractController
{
    /**
     * @Route("/trick/details/{slug}", name="trick_details")
     */
    public function index(TrickRepository $repo, Request $request, ObjectManager $manager, $slug)
    {
        $trick = $repo->findOneBySlug($slug);

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            dump($comment);

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été enregistré !'
            );

            return $this->redirectToRoute('trick_details', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick_details/index.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
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
