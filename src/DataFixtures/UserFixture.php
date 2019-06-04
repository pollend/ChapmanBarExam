<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{

    public const ADMIN_USER_REFERENCE = 'admin-user';

    private  $passwordEncoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        Collection::times(200,function ($index) use($faker,$manager){
            $user = new User($faker->unique()->name, ['user'], $faker->unique()->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user,'password'));
            $user->setEmailVerifiedAt($faker->dateTime());
            $user->setRememberToken(Str::random(10));
            $user->setAzureId('--');
            $manager->persist($user);
            return $user;
        });
        $manager->flush();


        $user = new User($faker->unique()->name, ['user','admin'], $faker->unique()->email);
        $password = $this->passwordEncoder->encodePassword($user,'password');
        $user->setPassword($password);
        $user->setEmailVerifiedAt($faker->dateTime());
        $user->setRememberToken(Str::random(10));
        $user->setAzureId('--');
        $manager->persist($user);
        $this->addReference(self::ADMIN_USER_REFERENCE,$user);

        $manager->flush();
    }


}