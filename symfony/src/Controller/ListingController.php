<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Listing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1')]
final class ListingController extends AbstractController
{

    public const ITEMS_PER_PAGE = 2;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager) {}

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/listings', name: 'get_listings', methods: [Request::METHOD_GET])]
    #[IsGranted("ROLE_ADMIN")]
    public function getListings(Request $request): JsonResponse
    {
        // $this->getUser(); // Depending on whether security is fully set up

        $queryParams = $request->query->all();

        $page = $queryParams['page'] ?? 1;
        $itemsPerPage = $queryParams['itemsPerPage'] ?? self::ITEMS_PER_PAGE;

        unset($queryParams['page']);
        unset($queryParams['itemsPerPage']);

        /** @var Listing $listing */
        $listings = $this->entityManager->getRepository(Listing::class)->getListings($queryParams, $page, $itemsPerPage);

        return new JsonResponse(['data' => $listings], Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return JsonResponse
     */
    #[Route('/listings/{id}', name: 'get_listing_item', methods: [Request::METHOD_GET])]
    public function getListingItem(string $id): JsonResponse
    {
        /** @var Listing $listing */
        $listing = $this->entityManager->getRepository(Listing::class)->find($id);

        if (!$listing) {
            return new JsonResponse(['data' => ['error' => 'Not found listing by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(['data' => $listing], Response::HTTP_OK);
    }

    #[Route('/listings', name: 'post_listings', methods: [Request::METHOD_POST])]
    public function createListing(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        /** @var Category $category */
        $category = $this->entityManager->getRepository(Category::class)->find($requestData['category']);

        if (!$category) {
            return new JsonResponse(['data' => ['error' => 'Not found category by id ' . $requestData['category']]], Response::HTTP_NOT_FOUND);
        }

        $listing = new Listing();

        $listing->setName($requestData['name'])
            ->setDescription($requestData['description'])
            ->setPrice($requestData['price'])
            ->setCategory($category);

        $this->entityManager->persist($listing);
        $this->entityManager->flush();

        return new JsonResponse([
            'data' => $listing
        ], Response::HTTP_CREATED);
    }

    #[Route('/listings/{id}', name: 'patch_listings', methods: [Request::METHOD_PATCH])]
    public function updateListing(string $id, Request $request): JsonResponse
    {
        /** @var Listing $listing */
        $listing = $this->entityManager->getRepository(Listing::class)->find($id);

        if (!$listing) {
            return new JsonResponse(['data' => ['error' => 'Not found listing by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        $listing->setPrice($requestData['price']);

        $this->entityManager->flush();

        return new JsonResponse([
            'data' => $listing
        ], Response::HTTP_OK);
    }

    #[Route('/listings/{id}', name: 'delete_listings', methods: [Request::METHOD_DELETE])]
    public function deleteListing(string $id): JsonResponse
    {
        /** @var Listing $listing */
        $listing = $this->entityManager->getRepository(Listing::class)->find($id);

        if (!$listing) {
            return new JsonResponse(['data' => ['error' => 'Not found listing by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($listing);
        $this->entityManager->flush();

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

}
