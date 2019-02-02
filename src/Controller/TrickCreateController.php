<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Service\Cropper16x9;
use App\Service\UploadImage;
use App\Service\ThumbnailResizer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickCreateController extends AbstractController
{
    /**
     * @Route("/trick/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, ObjectManager $manager, Cropper16x9 $cropper16x9, UploadImage $uploadImage, ThumbnailResizer $thumbnailResizer)
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

        return $this->render('trick_create/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
