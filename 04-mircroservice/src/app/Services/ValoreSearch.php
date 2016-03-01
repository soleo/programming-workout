<?php
namespace App\Services;

use App\Contracts\Search;
use Packback\Prices\Clients\ValoreBooksPriceClient as Client;
use Redis;

class ValoreSearch implements Search
{
    protected $client;

    public function __construct(Client $c)
    {
        $this->client = $c;
    }

    public function get($query)
    {
        $isbns = explode(',', $query);
        // normalize it
        $normalizedISBNs = [];
        $cachedResults = [];
        foreach ($isbns as $isbn) {
            // remove all non-numeric but keep X or x, because our vendors didn't deal with different format
            $normalizedISBN = preg_replace('/[^0-9Xx]/', '', $isbn);
            if (strlen($normalizedISBN) >= 10) {
                Redis::incr("book:search:".$normalizedISBN);
            }
            $redisKey = 'book:price:'.$normalizedISBN;
            if (Redis::exists($redisKey)) {
                $cachedResult = Redis::get($redisKey);
                $cachedResults = array_merge($cachedResults, json_decode($cachedResult, true));
            } else {
                $normalizedISBNs[] = $normalizedISBN;
            }
        }
        $prices = [];
        if (!empty($normalizedISBNs)) {
            $prices = $this->client->getPricesForIsbns($normalizedISBNs);
        }

        // caching valid results for a minute
        foreach ($normalizedISBNs as $isbn) {
            // only cache ISBN13 for now
            if (strlen($isbn) <= 10) {
                continue;
            }
            $book = [];
            foreach ($prices as $price) {
                // @TODO: need to be able to convert isbn10 to isbn13 for better caching
                if ($price->isbn13 === $isbn) {
                    $book[] = $price;
                }
            }
            if (!empty($isbn)) {
                Redis::set('book:price:'.$isbn, json_encode($book));
                Redis::ttl("expire in 1 minute");
            }
        }
        $response = array_merge($prices, $cachedResults);
        return $response;
    }
}
