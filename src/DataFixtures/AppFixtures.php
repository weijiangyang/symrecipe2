<?php

namespace App\DataFixtures;


use Faker\Factory;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    private Generator $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr-FR');
    }
    public function load(ObjectManager $manager): void
    {
       
        // ingredients
        $ingredients = [];
        for($i = 0 ; $i<50; $i++){
             $ingredient = new Ingredient;
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0,150));
            $manager->persist($ingredient);
            $ingredients[] = $ingredient;
        }

        // recettes
        $recipes = [];
        for ($j = 0; $j < 25; $j++) {
            $recipe = new Recipe;
            $recipe->setName($this->faker->word())
                ->setTime(mt_rand(0,1) == 1? mt_rand(1,1440) : null)
                ->setNbPeople(mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulty(mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription($this->faker->text(300))
                ->setIsFavorite(mt_rand(0, 1) == 1 ? true : false)
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null);
            for($k = 0 ; $k < mt_rand(5,15); $k++){
                $recipe->addIngredient($ingredients[mt_rand(0,count($ingredients) - 1)]);
            }
                
            $manager->persist($recipe);
            $recipes[] = $recipe;
        }


    $manager->flush();
    }
}
