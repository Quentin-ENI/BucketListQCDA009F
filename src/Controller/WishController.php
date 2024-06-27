<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use App\Service\CensuratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/wishes', name: 'wish_')]
class WishController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        WishRepository $wishRepository
    ): Response
    {
        $wishes = $wishRepository->findWishesAndCategory();

        return $this->render('wish/list.html.twig', [
            'wishes' => $wishes
        ]);
    }

    #[Route('/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(
        WishRepository $wishRepository,
        int $id
    ): Response
    {
        $wish = $wishRepository->find($id);

        return $this->render('wish/read.html.twig', [
            'wish' => $wish
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED')]
    public function create(
        EntityManagerInterface $entityManager,
        Request $request,
        CensuratorService $censuratorService
    ): Response {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {

            $wish->setAuthor($this->getUser());

            $wish->setDateCreated(new \DateTime());
            $wish->setDateUpdated(new \DateTime());

            $wish->setDescription(
                $censuratorService->purify($wish->getDescription())
            );

            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', "Idea successfully added!");

            return $this->redirectToRoute('wish_read', [
                'id' => $wish->getId()
            ]);
        }

        return $this->render('wish/create.html.twig', [
            'form' => $wishForm
        ]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function update(
        EntityManagerInterface $entityManager,
        WishRepository $wishRepository,
        Request $request,
        CensuratorService $censuratorService,
        int $id
    ): Response {

        $loggedInUser = $this->getUser();
        $wish = $wishRepository->find($id);

        if (!($wish->getAuthor()->getUsername() === $loggedInUser->getUserIdentifier())) {
            $this->addFlash('danger', "Le souhait ne peut pas être modifié");
            return $this->redirectToRoute('wish_read', ['id' => $id]);
        }

        $wishForm = $this->createForm(WishType::class, $wish);

        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setDateUpdated(new \DateTime());

            $wish->setDescription(
                $censuratorService->purify($wish->getDescription())
            );

            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', "Idea successfully updated!");

            return $this->redirectToRoute('wish_read', [
                'id' => $wish->getId()
            ]);
        }

        return $this->render('wish/update.html.twig', [
            'form' => $wishForm
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        WishRepository $wishRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $wish = $wishRepository->find($id);
        $loggedInUser = $this->getUser();

        // Tests
        // Scénario 1 : L'utilisateur connecté est l'auteur et il n'est pas ROLE_ADMIN - OK
        // Scénario 2 : L'utilisateur connecté n'est pas l'auteur et il n'est pas ROLE_ADMIN - OK
        // Scénario 3 : L'utilisateur connecté est l'auteur et il est ROLE_ADMIN - OK
        // Scénario 4 : L'utilisateur connecté n'est pas l'auteur et il est ROLE_ADMIN - OK

        if (
            !(
                $wish->getAuthor()->getUsername() === $loggedInUser->getUserIdentifier() ||
                in_array('ROLE_ADMIN', $loggedInUser->getRoles())
            )
        ) {
            $this->addFlash('danger', "Le souhait ne peut pas être supprimé");
            return $this->redirectToRoute('wish_read', ['id' => $id]);
        }

        $entityManager->remove($wish);
        $entityManager->flush();

        $this->addFlash('success', "Le souhait a bien été supprimé.");

        return $this->redirectToRoute('wish_list');
    }
}
