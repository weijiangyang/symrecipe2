<?php

namespace App\DataFixtures;


use Faker\Factory;

use App\Entity\Mark;
use App\Entity\User;
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
        // utilisateurs
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User;
            $user->setFullName($this->faker->name())
                ->setPseudo(mt_rand(0, 1) === 1 ? $this->faker->name() : null)
                ->setEmail($this->faker->email())
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('password');
            $users[] = $user;

            $manager->persist($user);
        }

       
        // ingredients
        $ingredients = [];
        for($i = 0 ; $i<50; $i++){
             $ingredient = new Ingredient;
            $ingredient->setName($this->faker->word())
                ->setPrice(mt_rand(0,150))
                ->setUser($users[mt_rand(0,count($users) - 1)]);
            
                
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
                ->setUser($users[mt_rand(0, count($users) - 1)])
                ->setIsPublic(mt_rand(0, 1) == 1 ? true : false)
                ->setPrice(mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null);
            for($k = 0 ; $k < mt_rand(5,15); $k++){
                $recipe->addIngredient($ingredients[mt_rand(0,count($ingredients) - 1)]);
            }
                
            $manager->persist($recipe);
            $recipes[] = $recipe;
        }

        //marks
      foreach($recipes as $recipe){
        $userschosen = [];
        for($i=0;$i<mt_rand(3,10);$i++){
            $userschosen[]= $users[mt_rand(0,count($users) - 1)];
        }
        $userschosen = array_unique($userschosen);

        foreach($userschosen as $userchosen){
            $mark = new Mark;
            $mark->setMark(mt_rand(1,5))
                ->setUser($userchosen)
                ->setRecipe($recipe);
            $manager->persist($mark);
        }
      }
           
         
    $manager->flush();
  }
}
