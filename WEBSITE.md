# Book API Documentation

## Overview

This API provides endpoints to search, filter, and retrieve book information. It supports various filtering options to refine search results and book listings.

## Base URL

https://api.majesticminds.com/v1

## Authentication

All API requests require authentication. Include your API key in the header of each request:

Authorization: Bearer YOUR_API_KEY

## Endpoints

### 1. List All Books

Retrieves a paginated list of books with optional filters.

GET /books

#### Query Parameters

| Parameter       | Type   | Description                                     |
| --------------- | ------ | ----------------------------------------------- |
| category[]      | array  | Filter by book categories                       |
| format[]        | array  | Filter by book formats (e.g., paperback, ebook) |
| price_min       | number | Minimum price                                   |
| price_max       | number | Maximum price                                   |
| publishing_year | number | Filter by year of publication                   |
| publisher[]     | array  | Filter by publishers                            |
| author[]        | array  | Filter by authors                               |
| page            | number | Page number for pagination (default: 1)         |

#### Example Request

GET /books?category[]=fiction&format[]=paperback&price_min=10&price_max=50&publishing_year=2022

#### Response

{
"data": [
{
"id": 1,
"title": "Sample Book",
"author": {
"id": 1,
"name": "John Doe"
},
"category": {
"id": 1,
"name": "Fiction"
},
"publisher": {
"id": 1,
"name": "Sample Publisher"
},
"price": 29.99,
"format": "paperback",
"publication_date": "2022-05-15"
},
// ... more books
],
"links": {
"first": "https://api.majesticminds.com/v1/books?page=1",
"last": "https://api.majesticminds.com/v1/books?page=5",
"prev": null,
"next": "https://api.majesticminds.com/v1/books?page=2"
},
"meta": {
"current_page": 1,
"from": 1,
"last_page": 5,
"path": "https://api.majesticminds.com/v1/books",
"per_page": 20,
"to": 20,
"total": 100
}
}

### 2. Search Books

Searches for books based on a query string and optional filters.

GET /books/search

#### Query Parameters

| Parameter       | Type   | Description                                     |
| --------------- | ------ | ----------------------------------------------- |
| q               | string | Search query (required)                         |
| category[]      | array  | Filter by book categories                       |
| format[]        | array  | Filter by book formats (e.g., paperback, ebook) |
| price_min       | number | Minimum price                                   |
| price_max       | number | Maximum price                                   |
| publishing_year | number | Filter by year of publication                   |
| publisher[]     | array  | Filter by publishers                            |
| author[]        | array  | Filter by authors                               |
| page            | number | Page number for pagination (default: 1)         |

#### Example Request

GET /books/search?q=branding&category[]=business&format[]=ebook&price_min=15&price_max=30

#### Response

Similar to the List All Books endpoint, but results are filtered based on the search query.

### 3. Get Book by ID

Retrieves detailed information about a specific book.

GET /books/{id}

#### Parameters

| Parameter | Type    | Description |
| --------- | ------- | ----------- |
| id        | integer | Book ID     |

#### Example Request

GET /books/123

#### Response

{
"id": 123,
"title": "The Art of Branding",
"author": {
"id": 45,
"name": "Jane Smith"
},
"category": {
"id": 3,
"name": "Business"
},
"publisher": {
"id": 12,
"name": "Business Press"
},
"price": 24.99,
"format": "ebook",
"publication_date": "2023-03-10",
"description": "A comprehensive guide to building and maintaining a strong brand...",
"isbn": "9781234567890"
}

### 4. Get Books by Category

Retrieves a list of books in a specific category.

GET /books/category/{categoryId}

#### Parameters

| Parameter  | Type    | Description |
| ---------- | ------- | ----------- |
| categoryId | integer | Category ID |

#### Example Request

GET /books/category/3

#### Response

Similar to the List All Books endpoint, but only includes books from the specified category.

### 5. Get Latest Books

Retrieves a list of the most recently published books.

GET /books/latest

#### Query Parameters

| Parameter | Type    | Description                             |
| --------- | ------- | --------------------------------------- |
| limit     | integer | Number of books to return (default: 10) |

#### Example Request

GET /books/latest?limit=5

#### Response

Returns an array of the most recent books, limited to the specified number.

### 6. Get Best-Selling Books

Retrieves a list of the best-selling books.

GET /books/best-sellers

#### Query Parameters

| Parameter | Type    | Description                             |
| --------- | ------- | --------------------------------------- |
| limit     | integer | Number of books to return (default: 10) |

#### Example Request

GET /books/best-sellers?limit=5

#### Response

Returns an array of the best-selling books, limited to the specified number.

## Error Responses

The API uses standard HTTP response codes to indicate the success or failure of requests.

-   200 OK: The request was successful.
-   400 Bad Request: The request was invalid or cannot be served.
-   401 Unauthorized: The request requires authentication.
-   404 Not Found: The requested resource could not be found.
-   500 Internal Server Error: The server encountered an unexpected condition.

Error responses will include a JSON object with more details about the error.

{
"error": {
"code": "RESOURCE_NOT_FOUND",
"message": "The requested book could not be found."
}
}

## Rate Limiting

The API implements rate limiting to prevent abuse. You are limited to 1000 requests per hour. If you exceed this limit, you'll receive a 429 Too Many Requests response.

## Changelog

-   2023-07-01: Added filtering options to book search and list endpoints.
-   2023-06-15: Initial release of the Book API.

For any questions or support, please contact support@majesticminds.com.
