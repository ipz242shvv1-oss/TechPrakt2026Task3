<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Repository\ListingRepository;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ListingController extends Controller
{

    public const ITEMS_PER_PAGE = 2;

    /**
     * @var ListingRepository
     */
    private ListingRepository $listingRepository;

    /**
     * @param ListingRepository $listingRepository
     */
    public function __construct(ListingRepository $listingRepository)
    {
        $this->listingRepository = $listingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getListings(Request $request): mixed
    {
        $queryParams = $request->all();

        $itemsPerPage = $queryParams['itemsPerPage'] ?? self::ITEMS_PER_PAGE;

        unset($queryParams['page']);
        unset($queryParams['itemsPerPage']);

        $listings = $this->listingRepository->getListings($queryParams, $itemsPerPage ?? self::ITEMS_PER_PAGE);

        return response()->json($listings, Response::HTTP_OK);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function getListingItem(string $id): mixed
    {
        $listing = Listing::find($id);

        if (!$listing) {
            return response()->json(['data' => ['error' => 'Not found listing by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $listing], Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function createListing(Request $request): mixed
    {
        $requestData = json_decode($request->getContent(), true);

        $category = Category::find($requestData['category']);

        if (!$category) {
            return response()->json(['data' => ['error' => 'Not found category by id ' . $requestData['category']]], Response::HTTP_NOT_FOUND);
        }

        $listing = $category->listings()->create([
            'name'        => $requestData['name'],
            'price'       => $requestData['price'],
            'description' => $requestData['description']
        ]);

        return response()->json([
            'data' => $listing
        ], Response::HTTP_CREATED);
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function deleteListing(string $id): mixed
    {
        $listing = Listing::find($id);

        if (!$listing) {
            return response()->json(['data' => ['error' => 'Not found listing by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $listing->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return mixed
     */
    public function updateListing(string $id, Request $request): mixed
    {
        $listing = Listing::find($id);

        if (!$listing) {
            return response()->json(['data' => ['error' => 'Not found listing by id ' . $id]], Response::HTTP_NOT_FOUND);
        }

        $requestData = json_decode($request->getContent(), true);

        $listing->update([
            'price' => $requestData['price']
        ]);

        return response()->json([
            'data' => $listing
        ], Response::HTTP_CREATED);
    }

}
