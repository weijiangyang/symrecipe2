<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\User;
use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    private function getEntity():Recipe 
    {
        $recipe = new Recipe;
        $recipe->setName('Recipe #1')
            ->setDescription('Description #1')
            ->setIsFavorite(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
        return $recipe;
    }
    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = self::getContainer();
       
        $errors = $container->get('validator')->validate($this->getEntity());
        $this->assertCount(0,$errors);
       
    }

    public function testInvalidName(){
        self::bootKernel();
        $container = self::getContainer();
        $recipe = $this->getEntity();
        $recipe->setName('');
        $errors = $container->get('validator')->validate($recipe);
        $this->assertCount(2, $errors);
    }

    public function testGetAverage(){
        $recipe = $this->getEntity();
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class,1);
    
        for($i = 0;$i<5;$i++){
            $mark = new Mark;
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);
            $recipe->addMark($mark);
            $this->assertTrue(2 === $recipe->getAverage());

        }
    }
}
