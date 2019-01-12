<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickCreateController extends AbstractController
{
    /**
     * @Route("/trick/create", name="trick_create")
     */
    public function index(Request $request, ObjectManager $manager)
    {
        $trick = new Trick();

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
                'Le trick <strong>' . $trick->getName() . '</strong> a bien été enregistré !'
            );

            return $this->redirectToRoute('trick_details', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('trick_create/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
