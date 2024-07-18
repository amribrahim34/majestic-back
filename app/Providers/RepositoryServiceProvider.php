<?php

// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use App\Repositories\AuthorRepository;
use App\Repositories\BookRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\AuthorRepositoryInterface;
use App\Repositories\Interfaces\BookRepositoryInterface;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\Interfaces\PublisherRepositoryInterface;
use App\Repositories\Interfaces\Website\AuthorRepositoryInterface as WebsiteAuthorRepositoryInterface;
use App\Repositories\Interfaces\Website\BlogPostRepositoryInterface;
use App\Repositories\Interfaces\Website\BookRepositoryInterface as WebsiteBookRepositoryInterface;
use App\Repositories\Interfaces\Website\CartRepositoryInterface;
use App\Repositories\Interfaces\Website\CategoryRepositoryInterface as WebsiteCategoryRepositoryInterface;
use App\Repositories\Interfaces\Website\PublisherRepositoryInterface as WebsitePublisherRepositoryInterface;
use App\Repositories\Interfaces\Website\SocialAuthRepositoryInterface;
use App\Repositories\Interfaces\WishListRepositoryInterface;
use App\Repositories\LanguageRepository;
use App\Repositories\PostRepository;
use App\Repositories\PublisherRepository;
use App\Repositories\Website\AuthorRepository as WebsiteAuthorRepository;
use App\Repositories\Website\BlogPostRepository;
use App\Repositories\Website\BookRepository as WebsiteBookRepository;
use App\Repositories\Website\CartRepository;
use App\Repositories\Website\CategoryRepository as WebsiteCategoryRepository;
use App\Repositories\Website\PublisherRepository as WebsitePublisherRepository;
use App\Repositories\Website\SocialAuthRepository;
use App\Repositories\WishListRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(AuthorRepositoryInterface::class, AuthorRepository::class);
        $this->app->bind(PublisherRepositoryInterface::class, PublisherRepository::class);
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(WebsiteBookRepositoryInterface::class, WebsiteBookRepository::class);
        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
        $this->app->bind(WishListRepositoryInterface::class, WishListRepository::class);
        $this->app->bind(SocialAuthRepositoryInterface::class, SocialAuthRepository::class);


        $this->app->bind(WebsiteBookRepositoryInterface::class, WebsiteBookRepository::class);
        $this->app->bind(WebsiteCategoryRepositoryInterface::class, WebsiteCategoryRepository::class);
        $this->app->bind(WebsitePublisherRepositoryInterface::class, WebsitePublisherRepository::class);
        $this->app->bind(WebsiteAuthorRepositoryInterface::class, WebsiteAuthorRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(BlogPostRepositoryInterface::class, BlogPostRepository::class);
    }
}
