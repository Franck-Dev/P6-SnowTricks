<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\Cropper16x9;
use App\Service\UploadImage;
use App\Service\ThumbnailResizer;
use App\Repository\TrickRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    /**
     * @Route("/trick/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     */
    public function create(Request $request, ObjectManager $manager, Cropper16x9 $cropper16x9, UploadImage $uploadImage, ThumbnailResizer $thumbnailResizer)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $mainImage = $trick->getMainImage();
            // Assignation du trick à l'image principale
            $mainImage->setTrick($trick);
            // Enregistrement de l'image sur le disque dur et en BDD
            $mainImage = $uploadImage->saveImage($mainImage);
            // On persiste l'entité Image une fois bien remplie dans la BDD
            $manager->persist($mainImage);

            // Enregistrement sur le disque de l'image redimensionnée en 16x9
            $cropper16x9->crop($mainImage);
            // Enregistrement sur le disque de l'image redimensionnée à la taille d'un thumbnail
            $thumbnailResizer->resize($mainImage);

            foreach($trick->getImages() as $image)
            {
                // Assignation du trick à l'image
                $image->setTrick($trick);
                // Enregistrement de l'image sur le disque dur et en BDD
                $image = $uploadImage->saveImage($image);
                // On persiste l'entité Image une fois bien remplie dans la BDD
                $manager->persist($image);
                
                // Enregistrement sur le disque de l'image redimensionnée en 16x9
                $cropper16x9->crop($image);
                // Enregistrement sur le disque de l'image redimensionnée à la taille d'un thumbnail (!!! A partir de l'image 16x9 !!!)
                $thumbnailResizer->resize($image);
            }

            foreach($trick->getVideos() as $video)
            {
                $video->setTrick($trick);
                $manager->persist($video);
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

        return $this->render('trick/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/trick/details/{slug}", name="trick_details")
     */
    public function details(TrickRepository $repo, Request $request, ObjectManager $manager, $slug)
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

        return $this->render('trick/details.html.twig', [
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

        return $this->render('trick/loadMoreComments.html.twig', [
            'trick' => $trick,
            'start' => $start
        ]);
    }

    /**
     * @Route("/trick/edit/{slug}", name="trick_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, TrickRepository $repo, ObjectManager $manager, $slug)
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

            $trick->setUpdatedAt(new \DateTime());

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

        return $this->render('trick/edit.html.twig', [
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

        $fileSystem = new Filesystem();

        foreach($trick->getImages() as $image)
        {
            $fileSystem->remove($image->getPath() . '/' . $image->getName());
            $fileSystem->remove($image->getPath() . '/cropped/' . $image->getName());
            $fileSystem->remove($image->getPath() . '/thumbnail/' . $image->getName());
        }

        $manager->remove($trick);
        $manager->flush();

        $this->addflash(
            'success',
            "Le trick <strong>{$trick->getName()}</strong> a été supprimé avec succès !"
        );

        return $this->redirectToRoute('home');
    }
}
