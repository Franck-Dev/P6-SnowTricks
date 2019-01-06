<?php

namespace App\Controller;

use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickEditController extends AbstractController
{
    /**
     * @Route("/trick/edit/{slug}", name="trick_edit")
     */
    public function index(Request $request, TrickRepository $repo, ObjectManager $manager, $slug)
    {
        $trick = $repo->findOneBySlug($slug);
        
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            foreach($trick->getImages() as $image)
            {
                $image->setTrick($trick);
                $manager->persist($image);
            }

            $trick->setCreatedAt(new \DateTime());
            $trick->setUpdatedAt(new \DateTime());
            $trick->setUser($this->getUser());

            $manager->persist($trick);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le trick <strong>' . $trick->getName() . '</strong> a bien été modifié !'
            );

            return $this->redirectToRoute('trick_details', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick_edit/index.html.twig', [
            'form' => $form->createView(),
            'trick' => $trick
        ]);
    }
}
