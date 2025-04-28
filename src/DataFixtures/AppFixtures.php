<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Operation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Change to UserPasswordHasherInterface

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Creating some users
        $user1 = new User();
        $user1->setEmail('john@user.com');
        $user1->setUsername('john');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'pass123')); // Updated to use hashPassword
        
        $user2 = new User();
        $user2->setEmail('admin@user.com');
        $user2->setUsername('admin');
        $user2->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'admin123')); // Updated to use hashPassword

        $manager->persist($user1);
        $manager->persist($user2);

        // Creating categories for User 1
        $category1 = new Category();
        $category1->setTitle('Food');
        $category1->setUser($user1);

        $category2 = new Category();
        $category2->setTitle('Clothing');
        $category2->setUser($user1);

        // Creating categories for User 2
        $category3 = new Category();
        $category3->setTitle('Transport');
        $category3->setUser($user2);

        $category4 = new Category();
        $category4->setTitle('Entertainment');
        $category4->setUser($user2);

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);

        // Creating operations for User 1
        $operation1 = new Operation();
        $operation1->setLabel('Grocery Shopping');
        $operation1->setAmount(50);
        $operation1->setDatetime(new \DateTime());
        $operation1->setUser($user1);
        $operation1->setCategory($category1);

        $operation2 = new Operation();
        $operation2->setLabel('Clothing Purchase');
        $operation2->setAmount(120);
        $operation2->setDatetime(new \DateTime());
        $operation2->setUser($user1);
        $operation2->setCategory($category2);

        $operation3 = new Operation();
        $operation3->setLabel('Dining Out');
        $operation3->setAmount(30);
        $operation3->setDatetime(new \DateTime());
        $operation3->setUser($user1);
        $operation3->setCategory($category1);

        // Creating operations for User 2
        $operation4 = new Operation();
        $operation4->setLabel('Bus Ticket');
        $operation4->setAmount(2.5);
        $operation4->setDatetime(new \DateTime());
        $operation4->setUser($user2);
        $operation4->setCategory($category3);

        $operation5 = new Operation();
        $operation5->setLabel('Cinema Ticket');
        $operation5->setAmount(15);
        $operation5->setDatetime(new \DateTime());
        $operation5->setUser($user2);
        $operation5->setCategory($category4);

        $operation6 = new Operation();
        $operation6->setLabel('Concert Ticket');
        $operation6->setAmount(50);
        $operation6->setDatetime(new \DateTime());
        $operation6->setUser($user2);
        $operation6->setCategory($category4);

        $manager->persist($operation1);
        $manager->persist($operation2);
        $manager->persist($operation3);
        $manager->persist($operation4);
        $manager->persist($operation5);
        $manager->persist($operation6);

        $manager->flush();
    }
}