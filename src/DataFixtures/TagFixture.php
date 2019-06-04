<?php


namespace App\DataFixtures;


use App\Entity\QuestionTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Illuminate\Support\Collection;

class TagFixture extends  Fixture
{
    public const TAG_REFERENCE = 'tag';


    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        Collection::times(50,function ($index) use ($manager,$faker) {
            $tag = new QuestionTag();
            $tag->setName($faker->word);
            $manager->persist($tag);
            return $tag;
        });
        $manager->flush();
    }
}