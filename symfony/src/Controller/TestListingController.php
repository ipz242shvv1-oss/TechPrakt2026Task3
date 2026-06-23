<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class TestListingController extends AbstractController
{

    public const DATA = [
        [
            'id'          => "1",
            'name'        => 'Listing1',
            'description' => 'Description 1',
            'price'       => '100'
        ],
        [
            'id'          => "2",
            'name'        => 'Listing2',
            'description' => 'Description 2',
            'price'       => '200'
        ],
        [
            'id'          => "3",
            'name'        => 'Listing3',
            'description' => 'Description 3',
            'price'       => '300'
        ],
        [
            'id'          => "4",
            'name'        => 'Listing4',
            'description' => 'Description 4',
            'price'       => '400'
        ]
    ];

    #[Route('/listings', name: 'test_get_listings', methods: ['GET'])]
    public function getListings(): Response
    {
        return new JsonResponse(self::DATA);
    }

    #[Route('/listings/{id}', name: 'test_get_listing_by_id', methods: ['GET'])]
    public function getListingById(string $id): Response
    {
        foreach (self::DATA as $listing) {
            if ($listing['id'] === $id) {
                return new JsonResponse($listing);
            }
        }

        return new JsonResponse();
    }

    #[Route('/listings', name: 'test_create_listing', methods: ['POST'])]
    public function createListing(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $id = rand(5, 100);

        $data['id'] = $id;

        return new JsonResponse($data, Response::HTTP_CREATED);
    }

    #[Route('/listings/{id}', name: 'test_update_listing', methods: ['PATCH'])]
    public function updateListing(string $id, Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $oldListing = null;

        foreach (self::DATA as $listing) {
            if ($listing['id'] === $id) {
                $oldListing = $listing;
            }
        }

        if (!$oldListing) {
            throw new NotFoundHttpException();
        }

        if (isset($data['name'])) {
            $oldListing['name'] = $data['name'];
        }

        if (isset($data['description'])) {
            $oldListing['description'] = $data['description'];
        }

        if (isset($data['price'])) {
            $oldListing['price'] = $data['price'];
        }

        return new JsonResponse($oldListing);
    }

    #[Route('/listings/{id}', name: 'test_delete_listing', methods: ['DELETE'])]
    public function deleteListing(string $id): Response
    {
        foreach (self::DATA as $listing) {
            if ($listing['id'] === $id) {
                return new JsonResponse(null, Response::HTTP_NO_CONTENT);
            }
        }

        throw new NotFoundHttpException();
    }

}
