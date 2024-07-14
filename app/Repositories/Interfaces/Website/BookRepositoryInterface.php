<?php

namespace App\Repositories\Interfaces\Website;

interface BookRepositoryInterface
{
    public function getBookById($id);
    public function getBooksByCategory($categoryId);
    public function getLatestBooks($limit = 10);
    public function getBestSellingBooks($limit = 10);
    public function getAllBooks(array $filters = []);
    public function searchBooks($query, array $filters = []);
    public function getPriceRange();
    public function getYearRange();
    public function getDistinctFormats();
}
