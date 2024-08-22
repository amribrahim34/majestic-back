<?php

// app/Providers/RepositoryServiceProvider.php

namespace App\Providers;

use App\Repositories\Admin\AuthorRepository;
use App\Repositories\Admin\BookRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Admin\CategoryRepository;
use App\Repositories\Admin\Dashboard\CustomerInsightsRepository;
use App\Repositories\Admin\Dashboard\OrderStatusRepository;
use App\Repositories\Admin\Dashboard\ProductPerformanceRepository;
use App\Repositories\Admin\Dashboard\SalesRepository;
use App\Repositories\Interfaces\Admin\AuthorRepositoryInterface;
use App\Repositories\Interfaces\Admin\BookRepositoryInterface;
use App\Repositories\Interfaces\Admin\CategoryRepositoryInterface;
use App\Repositories\Interfaces\Admin\LanguageRepositoryInterface;
use App\Repositories\Interfaces\Admin\OrderRepositoryInterface as InterfacesOrderRepositoryInterface;
use App\Repositories\Interfaces\Admin\PostRepositoryInterface;
use App\Repositories\Interfaces\Admin\PublisherRepositoryInterface;
use App\Repositories\Interfaces\Website\AuthorRepositoryInterface as WebsiteAuthorRepositoryInterface;
use App\Repositories\Interfaces\Website\BlogPostRepositoryInterface;
use App\Repositories\Interfaces\Website\BookRatingRepositoryInterface;
use App\Repositories\Interfaces\Website\BookRepositoryInterface as WebsiteBookRepositoryInterface;
use App\Repositories\Interfaces\Website\CartRepositoryInterface;
use App\Repositories\Interfaces\Website\CategoryRepositoryInterface as WebsiteCategoryRepositoryInterface;
use App\Repositories\Interfaces\Website\OrderRepositoryInterface;
use App\Repositories\Interfaces\Website\PublisherRepositoryInterface as WebsitePublisherRepositoryInterface;
use App\Repositories\Interfaces\Website\SocialAuthRepositoryInterface;
use App\Repositories\Interfaces\Website\WishListRepositoryInterface;
use App\Repositories\Admin\LanguageRepository;
use App\Repositories\Admin\OrderRepository as RepositoriesOrderRepository;
use App\Repositories\Admin\PostRepository;
use App\Repositories\Admin\PublisherRepository;
use App\Repositories\Interfaces\Admin\Dashboard\CustomerInsightsRepositoryInterface;
use App\Repositories\Interfaces\Admin\Dashboard\OrderStatusRepositoryInterface;
use App\Repositories\Interfaces\Admin\Dashboard\ProductPerformanceRepositoryInterface;
use App\Repositories\Interfaces\Admin\Dashboard\SalesRepositoryInterface;
use App\Repositories\Website\AuthorRepository as WebsiteAuthorRepository;
use App\Repositories\Website\BlogPostRepository;
use App\Repositories\Website\BookRatingRepository;
use App\Repositories\Website\BookRepository as WebsiteBookRepository;
use App\Repositories\Website\CartRepository;
use App\Repositories\Website\CategoryRepository as WebsiteCategoryRepository;
use App\Repositories\Website\OrderRepository;
use App\Repositories\Website\PublisherRepository as WebsitePublisherRepository;
use App\Repositories\Website\SocialAuthRepository;
use App\Repositories\Website\WishListRepository;

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
        $this->app->bind(InterfacesOrderRepositoryInterface::class, RepositoriesOrderRepository::class);
        $this->app->bind(SalesRepositoryInterface::class, SalesRepository::class);
        $this->app->bind(ProductPerformanceRepositoryInterface::class, ProductPerformanceRepository::class);
        $this->app->bind(OrderStatusRepositoryInterface::class, OrderStatusRepository::class);
        $this->app->bind(CustomerInsightsRepositoryInterface::class, CustomerInsightsRepository::class);



        $this->app->bind(WebsiteBookRepositoryInterface::class, WebsiteBookRepository::class);
        $this->app->bind(WebsiteCategoryRepositoryInterface::class, WebsiteCategoryRepository::class);
        $this->app->bind(WebsitePublisherRepositoryInterface::class, WebsitePublisherRepository::class);
        $this->app->bind(WebsiteAuthorRepositoryInterface::class, WebsiteAuthorRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(BlogPostRepositoryInterface::class, BlogPostRepository::class);
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(BookRatingRepositoryInterface::class, BookRatingRepository::class);
    }
}
