<?php

namespace App\Http\Controllers;

use Validator;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Contracts\Search;
use Redis;

class BookController extends Controller
{
    /**
     * @SWG\Info(title="Book Finder API", version="0.1")
     */

    /**
     * @SWG\Get(
     *     path="/book/search",
     *     summary="Search by ISBN",
     *     description="Return Book Prices",
     *     operationId="search",
     *     tags={"book"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         name="q",
     *         in="query",
     *         description="Single ISBN or ISBNs with comma ",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(response="200", description="Error Messages or Book Objects")
     * )
     */
    public function search(Search $search, Request $request)
    {
        // validate query
        $validator = Validator::make($request->all(), [
            'q' => 'bail|required|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
                ]);
        }

        $q = $request->input('q');

        $result = $search->get($q);

        if (empty($result)) {
            return response()->json([
                'errors' => ['We didn\'t find any books that matched your search.']
                ]);
        }

        return response()->json($result);
    }

    public function stats()
    {
        $redisKeys = Redis::keys('book:search:*');
        $stats = [];

        foreach ($redisKeys as $redisKey) {
            $stats[$redisKey] = Redis::get($redisKey);
        }

        return response()->json($stats);
    }
}
