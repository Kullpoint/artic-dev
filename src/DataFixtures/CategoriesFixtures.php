<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            [
                'topic' => 'Physics',
                'image' => 'physic.svg'
            ],
            [
                'topic' => 'Medicine',
                'image' => 'medicine.svg'
            ],
            [
                'topic' => 'Ecology',
                'image' => 'biology.svg'
            ],
            [
                'topic' => 'Language and literature of country',
                'image' => 'history.svg'
            ],
            [
                'topic' => 'English',
                'image' => 'relationships.svg'
            ],
            [
                'topic' => 'History',
                'image' => 'history.svg'
            ],
            [
                'topic' => 'Math',
                'image' => 'math.svg'
            ],
            [
                'topic' => 'Astronomy',
                'image' => 'physic.svg'
            ],
            [
                'topic' => 'Computer sciences',
                'image' => 'computerScience.svg'
            ],
            [
                'topic' => 'Culture',
                'image' => 'culture.svg'
            ],
            [
                'topic' => 'International relationships',
                'image' => 'relationships.svg'
            ],
            [
                'topic' => 'Philosophy',
                'image' => 'philosophy.svg'
            ],
            [
                'topic' => 'Psychology',
                'image' => 'psychology.svg'
            ],
            [
                'topic' => 'Economy',
                'image' => 'economy.svg'
            ],
            [
                'topic' => 'Anatomy',
                'image' => 'anatomy.svg'
            ],
            [
                'topic' => 'Biology',
                'image' => 'biology.svg'
            ],
        ];
        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->setTopic($category['topic']);
            $newCategory->setImage($category['image']);
            $manager->persist($newCategory);
            $manager->flush();
        }
    }
}
