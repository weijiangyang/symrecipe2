<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
         $this->assertSelectorTextContains('h1', 'Formulaie de Contact');

        // Récupere le formulaire
        $submitButton = $crawler->selectButton('Envoyer le message');
        $form = $submitButton->form();
        $form["contact[fullName]"] = 'weijiangyang';
        $form["contact[email]"] = 'weijiangyang@laposte.net';
        $form["contact[subject]"] = 'Test';
        $form["contact[message]"] = 'Test';


        //Soumetre le formulaire
         $client->submit($form);
        // // verifier le status HTTP
        //  $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        // verifier l'envoie de l'email
         //$this->assertEmailCount(1);
        // $client->followRedirect();
        // //verifier la presence du message du success
        // $this->assertSelectorTextContains(
        //     'div.alert.alert-success',
        //     'Votre message a bien été envoyé'
        // );
    }
}
