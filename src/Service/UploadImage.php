<?php

namespace App\Service;

use App\Entity\Image;

class UploadImage {

    /**
     * Créer un nom et un path pour l'image et l'enregistre sur le disque
     *
     * @param Image $image
     * @return Image $image
     */
    public function saveImage(Image $image): Image
    {
        // Récupère le fichier de l'image uploadée
        $file = $image->getFile();
        // Créer un nom unique pour le fichier
        $name = md5(uniqid()) . '.' . $file->guessExtension();
        // Déplace le fichier
        $path = 'img/tricks';
        $file->move($path, $name);

        // Donner le path et le nom au fichier dans la base de données
        $image->setPath($path);
        $image->setName($name);

        return $image;
    }

}