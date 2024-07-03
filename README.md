# Book Management API

## Overview

The Book Management API allows administrators to perform CRUD (Create, Read, Update, Delete) operations on books in the Majestic Minds bookstore system.

## Base URL

All API requests should be made to: `https://api.majesticminds.com/v1/admin/books`

## Authentication

All endpoints require authentication. Include the JWT token in the Authorization header:

## Endpoints

### 1. List Books

Retrieves a paginated list of books.

-   **URL:** `/`
-   **Method:** `GET`
-   **URL Params:**
    -   `page=[integer]` (optional, default=1)
    -   `per_page=[integer]` (optional, default=15)

#### Success Response:

-   **Code:** 200
-   **Content:**

    ```json
    {
        "data": [
            {
                "id": 1,
                "title": "Book Title",
                "author": {
                    "id": 1,
                    "first_name": { "en": "John", "ar": "يحيى" },
                    "last_name": { "en": "Doe", "ar": "الدو" }
                },
                "category": {
                    "id": 1,
                    "category_name": { "en": "Fiction", "ar": "خيال" }
                },
                "publisher": {
                    "id": 1,
                    "publisher_name": {
                        "en": "Publisher Name",
                        "ar": "اسم الناشر"
                    }
                },
                "language": {
                    "id": 1,
                    "language_name": "English"
                }
            }
            // ... more books
        ],
        "links": {
            "first": "https://api.majesticminds.com/v1/admin/books?page=1",
            "last": "https://api.majesticminds.com/v1/admin/books?page=10",
            "prev": null,
            "next": "https://api.majesticminds.com/v1/admin/books?page=2"
        },
        "meta": {
            "current_page": 1,
            "from": 1,
            "last_page": 10,
            "path": "https://api.majesticminds.com/v1/admin/books",
            "per_page": 15,
            "to": 15,
            "total": 150
        }
    }
    ```

### 2. Create a Book

Creates a new book.

-   **URL:** `/`
-   **Method:** `POST`
-   **Data Params:**

    ```json
    {
        "title": { "en": "New Book Title", "ar": "عنوان الكتاب الجديد" },
        "author_id": 1,
        "category_id": 1,
        "publisher_id": 1,
        "language_id": 1,
        "publication_date": "2023-01-01",
        "isbn10": "1234567890",
        "isbn13": "1234567890123",
        "num_pages": 200,
        "format": "Hard Copy",
        "price": 29.99,
        "stock_quantity": 100,
        "description": { "en": "Book description", "ar": "وصف الكتاب" },
        "img": "[base64 encoded image]"
    }
    ```

#### Success Response:

-   **Code:** 201
-   **Content:**

    ```json
    {
        "data": {
            "id": 2,
            "title": { "en": "New Book Title", "ar": "عنوان الكتاب الجديد" },
            "author": {
                "id": 1,
                "first_name": { "en": "John", "ar": "يحيى" },
                "last_name": { "en": "Doe", "ar": "الدو" }
            }
            // ... other book details
        }
    }
    ```

### 3. Get a Book

Retrieves details of a specific book.

-   **URL:** /:id
-   **Method:** `GET`
-   **URL Params:** `id=[integer]` (required)

#### Success Response:

-   **Code:** 200
-   **Content:** Same as the response for creating a book

### 4. Update a Book

Updates an existing book.

-   **URL:** /:id
-   **Method:** `PUT`
-   **URL Params:**

    -   `id=[integer]` (required)

-   **Data Params:** Same as for creating a book, all fields optional

#### Success Response:

-   **Code:** 200
-   **Content:** Same as the response for creating a book

### 5. Delete a Book

Deletes a specific book.

-   **URL:** /:id
-   **Method:** `DELETE`
-   **URL Params:**

    -   `id=[integer]` (required)

#### Success Response:

-   **Code:** 204
-   **Content:** No content

## Error Responses

### Code: 401 UNAUTHORIZED

-   **Content:** `{ "message": "Unauthenticated." }`

### OR

### Code: 403 FORBIDDEN

-   **Content:** `{ "message": "Unauthorized action." }`

### OR

### Code: 404 NOT FOUND

-   **Content:** `{ "message": "Book not found." }`

### OR

### Code: 422 UNPROCESSABLE ENTITY

-   **Content:**

    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "title": ["The title field is required."]
        }
    }
    ```

## Environment Variables

The following environment variables should be set:

-   `APP_URL`: The base URL of your application
-   `DB_DATABASE`: The name of your database
-   `DB_USERNAME`: Your database username
-   `DB_PASSWORD`: Your database password
-   `JWT_SECRET`: Secret key for JWT token generation

## Notes

-   All text fields (title, description) are translatable and should be provided as objects with language keys (e.g., "en", "ar").
-   The `img` field for creating/updating a book should be a base64 encoded string of the image file.
-   Ensure proper error handling and validation in your client application when interacting with these endpoints.
