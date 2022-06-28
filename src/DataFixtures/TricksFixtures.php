<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Tricks;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TricksFixtures extends Fixture
{
    protected $slugger;

    public function __construct(UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {
        $this->encoder = $encoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $user = [];
        $categorie = ['Grabs', 'Rotations', 'Flips', 'Slides', 'Misaligned Rotations', 'One foot', 'Old school'];
        $tricksName = ['Nose Grab', 'Japan Air', '720', '1080', 'Mac Twist', 'Hakon Flip', 'Rodeo', 'Misty', 'Nose Slide', 'Tail Slide', 'One Footers', 'One foot Indy', 'Method Air', 'Backside Air','Rocket Air'];

        for ($u=0; $u<1; $u++) 
        {
            $user = new User();
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setEmail($faker->safeEmail)
                ->setFirstname($faker->firstName())
                ->setLastname($faker->lastName());

            $password = $this->encoder->hashPassword($user, 'password');

            $user->setPassword($password);
            $manager->persist($user);
        }

        foreach ($categorie as $cat) 
        {
            $categorie = new Category();
            $categorie->setName($cat);
            $manager->persist($categorie);
            $cats[] = $categorie;
        }

        foreach ($tricksName as $test) 
        {
            $tricks = new Tricks();
            $img = new Media();
            $img->setName('/default.jpg');    
            $tricks->setTitle($test)
                ->setContent($faker->text(40))
                ->setCreatedAt(new DateTimeImmutable())
                ->setSlug(strtolower($this->slugger->slug($tricks->getTitle())))
                ->addMedia($img)
                ->setCategory($faker->randomElement($cats))
                ->setUser($user);

            $manager->persist($tricks);
        

            for ($k=0; $k<4; $k++) 
            {
                $comment = new Comment();
                $comment->setContent($faker->text(40))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setTricks($tricks);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }
}
