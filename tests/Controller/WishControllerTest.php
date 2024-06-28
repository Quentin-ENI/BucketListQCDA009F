<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class WishControllerTest extends WebTestCase
{
    public function test_getWishesCreate_withNotAuthenticatedUser(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/wishes/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Please sign in');
    }

    public function test_getWishesCreate_withAuthenticatedUser(): void {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'toto@yahoo.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/wishes/create');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Add your wishes!');
    }

    public function test_postWishesCreate_withAuthenticatedUserAndValidData(): void {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'toto@yahoo.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/wishes/create');

        $client->submitForm("Save", [
            'wish[title]' => 'Tout va bien',
            'wish[description]' => 'Tout va bien aussi',
            'wish[category]' => '6'
        ]);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();
        $this->assertRouteSame('wish_read');
    }

    public function test_postWishesCreate_withAuthenticatedUserAndNotValidTitle(): void {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'toto@yahoo.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/wishes/create');

        $client->submitForm("Save", [
            'wish[title]' => 'T',
            'wish[description]' => 'Tout va bien aussi',
            'wish[category]' => '6'
        ]);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('li', "Le titre doit avoir une longueur d'au moins 2 caractères");
        $this->assertRouteSame('wish_create');
    }
}
