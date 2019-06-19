<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'admin-user';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        Collection::times(200, function ($index) use ($faker,$manager) {
            $user = new User();
            $user->setRoles([User::ROLE_USER]);
            $user->setEmail($faker->unique()->email);
            $user->setUsername($faker->unique()->name);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
            $user->setEmailVerifiedAt($faker->dateTime());
            $user->setRememberToken(Str::random(10));
            $user->setAzureId('--');
            $manager->persist($user);

            return $user;
        });
        $manager->flush();

        Collection::times(10, function ($index) use ($faker,$manager) {
            $user = new User();
            $user->setRoles([User::ROLE_USER, User::ROLE_ADMIN]);
            $user->setEmail($faker->unique()->email);
            $user->setUsername($faker->unique()->name);
            $password = $this->passwordEncoder->encodePassword($user, 'password');
            $user->setPassword($password);
            $user->setEmailVerifiedAt($faker->dateTime());
            $user->setRememberToken(Str::random(10));
            $user->setAzureId('--');
            $manager->persist($user);
        });

        $manager->flush();
    }
}
