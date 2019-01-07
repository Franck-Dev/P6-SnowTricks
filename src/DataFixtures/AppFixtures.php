<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('FR-fr');

        $users = [];
        $categories = [];
        $genders = ['male', 'female'];

        // 20 User
        for ($i=0; $i<20; $i++)
        {
            $user = new User();

            $gender = $faker->randomElement($genders);
            $imageUrl = 'https://randomuser.me/api/portraits/' . ($gender == 'male' ? 'men/' : 'women/') . $faker->numberBetween(1,99) . '.jpg';

            $user->setUsername($faker->userName)
                 ->setEmail($faker->safeEmail)
                 ->setPassword($this->encoder->encodePassword($user, 'password'))
                 ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                 ->setImageUrl($imageUrl);
            
            $manager->persist($user);
            $users[] = $user;
        }

        // 10 Category
        for ($h=0; $h<10; $h++)
        {
            $category = new Category();
            $category->setName($faker->sentence(2));

            $manager->persist($category);
            $categories[] = $category;
        }

        // 40 Tricks
        for ($j=0; $j<40; $j++)
        {
            $trick = new Trick();
            $trick->setName($faker->sentence(3))
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setCreatedAt(new \Datetime)
                ->setUpdatedAt(new \Datetime)
                ->setMainImageUrl($faker->imageUrl(1000, 500))
                ->setUser($faker->randomElement($users))
                ->setCategory($faker->randomElement($categories));

            $manager->persist($trick);

            // 1 to 4 Image by Trick
            for ($k=0; $k<mt_rand(1, 4); $k++)
            {
                $image = new Image();
                $image->setUrl($faker->imageUrl(800, 450))
                      ->setTrick($trick);

                $manager->persist($image);
            }

            // 1 to 2 Video by Trick
            for ($l=0; $l<mt_rand(1, 2); $l++)
            {
                $video = new Video();
                $video->setUrl('https://youtu.be/FYQesbQXCac')
                      ->setTrick($trick);
                
                $manager->persist($video);
            }

            // 0 to 30 Comment by Trick
            for ($m=0; $m<mt_rand(0, 30); $m++)
            {
                $comment = new Comment();
                $comment->setContent($faker->sentence(mt_rand(1, 5)))
                        ->setCreatedAt(new \Datetime)
                        ->setUser($faker->randomElement($users))
                        ->setTrick($trick);
                
                $manager->persist($comment);
            }                
        }

        $manager->flush();
    }
}
