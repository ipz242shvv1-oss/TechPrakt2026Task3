<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function response;

class TestListingController extends Controller
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

    /**
     * @return Response
     */
    public function getListings(): Response
    {
        return response()->json(self::DATA, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return Response
     */
    public function getListingById(string $id): Response
    {
        foreach (self::DATA as $listing) {
            if ($listing['id'] === $id) {
                return response()->json($listing, Response::HTTP_OK);
            }
        }

        return response()->json([], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createListing(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $id = rand(5, 100);

        $data['id'] = $id;

        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return Response
     */
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

        return response()->json($oldListing, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return Response
     */
    public function deleteListing(string $id): Response
    {
        foreach (self::DATA as $listing) {
            if ($listing['id'] === $id) {
                return response()->json(null, Response::HTTP_NO_CONTENT);
            }
        }

        throw new NotFoundHttpException();
    }

}
