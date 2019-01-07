<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Entity\Category;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class TrickType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, $this->getOptions('Nom', 'Nom du trick'))
            ->add('description', TextareaType::class, $this->getOptions('Description', 'Description du trick'))
            ->add('slug', TextType::class, $this->getOptions('Chaîne URL (Slug)','Adresse web (automatique)', [
                'required' => false
            ]))
            ->add('category', EntityType::class, $this->getOptions('Catégorie','Catégorie du trick', [
                'class' => Category::class,
                'choice_label' => 'name'
            ]))
            ->add('mainImageUrl', UrlType::class, $this->getOptions('Image principale', 'Url de l\'image principale'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
