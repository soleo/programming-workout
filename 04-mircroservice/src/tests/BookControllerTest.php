<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Tests\Base;

class BookControllerTest extends TestCase
{
    /**
     * Search By Single ISBN
     *
     * @return void
     */
    public function testSearchBookBySingleISBN()
    {
        // Example ISBN 13: 9780321193858
        $this->get('/book/search?q=9780321193858')
             ->seeJsonStructure([
                 '*' => [
                     'isbn13',
                     'price',
                     'shipping_price',
                     'url',
                     'retailer',
                     'term',
                     'condition'
                 ]
             ]);
        // Ignore anything which is not a number
        $this->get('/book/search?q=978-0761178422')
             ->seeJsonStructure([
                 '*' => [
                     'isbn13',
                     'price',
                     'shipping_price',
                     'url',
                     'retailer',
                     'term',
                     'condition'
                 ]
             ]);
        // Search with ISBN 10 end with X
        $this->get('/book/search?q=0-8044-2957-X')
             ->seeJsonStructure([
                 '*' => [
                     'isbn13',
                     'price',
                     'shipping_price',
                     'url',
                     'retailer',
                     'term',
                     'condition'
                 ]
             ]);
    }

    /**
     * Search By Multiple ISBN
     *
     * @return void
     */
    public function testSearchBookByMultipleISBNs()
    {
        $this->get('/book/search?q=9780321193858,9780961584146')
             ->seeJsonStructure([
                 '*' => [
                     'isbn13',
                     'price',
                     'shipping_price',
                     'url',
                     'retailer',
                     'term',
                     'condition'
                 ]
             ]);
    }

    /**
     * Invaild Query String Test
     *
     * @return void
     */
    public function testInvalidQuery()
    {
        // No q query
        $this->get('/book/search')
             ->seeJsonEquals([
                 'errors' => [
                     'Search keyword must be provided, otherwise aliens will come and destory earth!'
                  ]
             ]);
        // Empty q query
        $this->get('/book/search?q=')
             ->seeJsonEquals([
                 'errors' => [
                     'Search keyword must be provided, otherwise aliens will come and destory earth!'
                  ]
             ]);
        // Space q query
        $this->get('/book/search?q=%20%20%20')
             ->seeJsonEquals([
                 'errors' => [
                     'Search keyword must be provided, otherwise aliens will come and destory earth!'
                  ]
             ]);
        // Let's face it, no one is going to provide a super long query only if someone trying to crawl. Max: 255
        $this->get('/book/search?q=9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146,9780321193858,9780961584146')
             ->seeJsonEquals([
                 'errors' => [
                     'Please be reasonable, are you sure you want to search so many books all at once?'
                  ]
             ]);
    }

    /**
     * No Result
     *
     * @return void
     */
    public function testFindNoResult()
    {
        $this->get('/book/search?q=9780761178423')
             ->seeJsonEquals([
                 'errors' => [
                     'We didn\'t find any books that matched your search.'
                  ]
             ]);
    }
}
