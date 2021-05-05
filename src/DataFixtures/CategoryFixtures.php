<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public $categoryList = [
        'Fantasy',
        'Science Fiction',
        'Dystopian',
        'Adventure',
        'Romance',
        'Detective & Mystery',
        'Horror',
        'Thriller',
        'Historical Fiction',
        'Young Adult (YA)',
        'Children’s Fiction',
        'Memoir & Autobiography',
        'Biography',
        'Cooking',
        'Art & Photography',
        'Self-Help / Personal Development',
        'Motivational / Inspirational',
        'Health & Fitness',
        'History',
        'Crafts, Hobbies & Home',
        'Families & Relationships',
        'Humor & Entertainment',
        'Business & Money',
        'Law & Criminology',
        'Politics & Social Sciences',
        'Religion & Spirituality',
        'Education & Teaching',
        'Travel',
        'True Crime'
    ];

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 0; $i < count($this->categoryList) ; $i++) {
            $category = new Category();
            $category->setName($this->categoryList[$i]);
            $manager->persist($category);
        }
        $manager->flush();
    }
}
