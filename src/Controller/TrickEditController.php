<?php

namespace App\Controller;

use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickEditController extends AbstractController
{
    /**
     * @Route("/trick/edit/{slug}", name="trick_edit")
     * @IsGranted("ROLE_USER")
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

    /**
     * @Route("/trick/delete/{slug}", name="trick_delete")
     * @IsGranted("ROLE_USER")
     */
    public function delete(TrickRepository $repo, ObjectManager $manager, $slug)
    {
        $trick = $repo->findOneBySlug($slug);

        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'success',
            "Le trick <strong>{$trick->getName()}</strong> a été supprimé avec succès !"
        );

        return $this->redirectToRoute('home');
    }
}
