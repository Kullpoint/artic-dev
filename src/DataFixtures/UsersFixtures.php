<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Partner;
use App\Entity\Performer;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersFixtures extends Fixture
{
    private $passwordEncoder;
    private $categoryRepository;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, CategoryRepository $categoryRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->categoryRepository = $categoryRepository;
    }

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime('01-01-2002');
        $users = [
            [
                'email' => 'performer@mail.ru',
                'phone' => '0978815482',
                'lastname' => 'Petrov',
                'firstname' => 'Ivan',
                'roles' => ['ROLE_PERFORMER'],
                'date' => $date,
                'country' => 'UA',
                'city' => 'Lutsk',
            ],
            [
                'email' => 'client@mail.ru',
                'phone' => '0958815482',
                'lastname' => 'Solomonov',
                'firstname' => 'Petro',
                'roles' => ['ROLE_USER'],
                'country' => 'UA',
                'city' => 'Lutsk',
            ],
            [
                'email' => 'partner@mail.ru',
                'phone' => '0938815482',
                'lastname' => 'Ivanov',
                'firstname' => 'Petro',
                'roles' => ['ROLE_PARTNER'],
                'date' => $date,
            ],
            [
                'email' => 'admin@mail.ru',
                'phone' => '0950000000',
                'lastname' => 'Admin',
                'firstname' => 'Admin',
                'roles' => ['ROLE_ADMIN'],
                'date' => $date,
            ],
        ];

        foreach ($users as $user) {
            $newUser = new User();
            $newUser->setPassword(
                $this->passwordEncoder->encodePassword(
                    $newUser,
                    '123'
                )
            );
            $newUser->setEmail($user['email']);
            $newUser->setPhone($user['phone']);
            $newUser->setLastName($user['lastname']);
            $newUser->setFirstName($user['firstname']);
            $newUser->setIsVerified(false);

            if ($user['roles'][0] === 'ROLE_PERFORMER') {
                $newUser->setRoles(['ROLE_PERFORMER']);
                $performer = new Performer();
                $performer->setBirthday($user['date']);
                $performer->setCountry($user['country']);
                $performer->setCity($user['city']);
                $performer->addCategory($this->categoryRepository->findAll()[0]);
                $performer->setExperience('10');
                $performer->setSummeryTypes('Math');
                $newUser->setPerformer($performer);
                $manager->persist($performer);
            } else if ($user['roles'][0] === 'ROLE_USER') {
                $client = new Client();
                $newUser->setRoles(['ROLE_USER']);
                $client->setCountry($user['country']);
                $client->setCity($user['city']);
                $newUser->setClient($client);
                $manager->persist($client);
            } else if ($user['roles'][0] === 'ROLE_PARTNER') {
                $partner = new Partner();
                $newUser->setRoles(['ROLE_PARTNER']);
                $referralCode = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
                $referralLink = $referralCode;
                $partner->setReferral($referralLink);
                $partner->setBirthday($user['date']);
                $newUser->setPartner($partner);
                $manager->persist($partner);
            }

            $manager->persist($newUser);
            $manager->flush();
        }
    }
}
