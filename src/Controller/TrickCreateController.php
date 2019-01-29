<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickCreateController extends AbstractController
{
    /**
     * Rogne l'image pour obtenir un format 16:9
     *
     * @return UploadedFile $file
     */
    private function cropTo16x9($path, $name, $extension)
    {
        if($extension === 'jpeg' || $extension === 'jpg') {
            $originalImg = imagecreatefromjpeg($path . '/' . $name);
        }
        else if($extension == 'png') {
            $originalImg = imagecreatefrompng($path . '/' . $name);
        }
        else { 
            // Affiche message flash d'erreur (Redirection vers la page de création de trick ?)
        }
        $ratio16x9 = 16 / 9;
        $ratio = imagesx($originalImg) / imagesy($originalImg);

        if(!(round($ratio, 2) === round($ratio16x9, 2)))
        {
            if($ratio < $ratio16x9)
            {
                $width = imagesx($originalImg);
                $height = (imagesx($originalImg) / 16) * 9;
            }
            else if($ratio > $ratio16x9)
            {
                $width = (imagesy($originalImg) / 9) * 16;
                $height = imagesy($originalImg);
            }

            $croppedImg = imagecrop($originalImg, ['x' => 0, 'y' => 0, 'width' => $width, 'height' => $height]);

            if ($croppedImg !== FALSE) {
                $name = md5(uniqid()) . '.jpeg';
                imagejpeg($croppedImg, $path . '/' . $name);
                imagedestroy($croppedImg);
                $file = new UploadedFile($path . '/' . $name, $name, 'jpeg', UPLOAD_ERR_OK, true);
            }
            imagedestroy($originalImg);

            return $file;
        }
        // Si déjà en format 16:9
        return $file;
    }

    private function saveImage(Image $image, Trick $trick)
    {
        //Assignation du trick à l'image
        $image->setTrick($trick);
        // Récupère le fichier de l'image si elle a été uploadée
        $file = $image->getFile();
        // Créer un nom unique pour le fichier
        $name = md5(uniqid()) . '.' . $file->guessExtension();
        $extension = $file->guessExtension();
        // Déplace le fichier
        $path = 'img/GD';
        $file->move($path, $name);
        // Redimension de l'image en 16:9
        $file = $this->cropTo16x9($path, $name, $extension);
        $image->setFile($file);
        // Déplace le fichier
        $path = 'img/tricks';
        $file->move($path, $name);
        // Donner un path et nom au fichier dans la base de données
        $image->setPath($path);
        $image->setName($name);

        return $image;
    }

    /**
     * @Route("/trick/create", name="trick_create")
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, ObjectManager $manager)
    {
        $trick = new Trick();

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $mainImage = $this->saveImage($trick->getMainImage(), $trick);
            $manager->persist($mainImage);

            foreach($trick->getImages() as $image)
            {
                $image = $this->saveImage($image, $trick);
                $manager->persist($image);
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
