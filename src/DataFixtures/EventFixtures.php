<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class EventFixtures extends Fixture
{
    public const NB_EVENTS = 40;

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create("fr_FR");

        $faker->addProvider(new \WW\Faker\Provider\Picture($faker));

        for ($i = 0; $i < self::NB_EVENTS; $i++) {
            $event = new Event();
            $event->setName($faker->sentence(3));
            $event->setDescription($faker->text(200));

            $event->setDate($faker->dateTimeBetween('+7 days', '+6 months'));

            $event->setPrice($faker->randomFloat(2, 0, 100));
            $event->setOnline($faker->boolean(70));
            $event->setQuantity(100);
            $event->setCreatedAt(new \DateTimeImmutable());

            $randomParam = $faker->unique()->randomNumber();
            $event->setPicture($faker->pictureUrl(1200, 400, false, 0) . "?random=" . $randomParam);

            $event->setGenerateImageByFixture(true);
            $manager->persist($event);
        }

        $manager->flush();
    }
}
