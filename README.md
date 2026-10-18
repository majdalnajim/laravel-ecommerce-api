Laravel E-commerce REST API

A RESTful e-commerce backend built with Laravel and MySQL, providing product and category management, shopping cart operations, order processing, and inventory management.

This project demonstrates practical backend development concepts, including API authentication, database relationships, request validation, resource responses, database transactions, authorization, and automated testing.

Features

* Authentication: User registration and login using Laravel Sanctum.
* Product Management: Create, retrieve, update, and delete products.
* Category Management: Organize products by category.
* Shopping Cart: Add products, update quantities, remove items, and clear the cart.
* Order Management: Create orders from cart items, view orders, and cancel eligible orders.
* Inventory Management: Validate stock availability and restore stock when eligible orders are cancelled.
* Authorization: Restrict protected operations according to authentication and authorization rules.
* API Resources: Structure API responses using Laravel API Resources.
* Search and Filtering: Search products and filter results.
* Pagination: Return product listings in manageable pages.
* Soft Deletes: Support soft deletion for products.
* Automated Tests: Feature tests for product, cart, and order workflows.

Tech Stack

* PHP
* Laravel
* MySQL
* Laravel Sanctum
* Eloquent ORM
* RESTful API
* PHPUnit / Laravel Testing

Requirements

* PHP version compatible with the project’s composer.json
* Composer
* MySQL
* Node.js and npm if frontend assets need to be built

Installation

1. Clone the repository:

git clone https://github.com/majdalnajim/laravel-ecommerce-api.git
cd laravel-ecommerce-api

2. Install PHP dependencies:

composer install

3. Create your environment file:

cp .env.example .env

    On Windows, you can copy .env.example to .env manually.
4. Generate the application key:

php artisan key:generate

5. Configure your database settings in .env:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ecommerce
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password

    Create the database before running the migrations.
6. Run database migrations:

php artisan migrate

7. Create the public storage link for uploaded product images:

php artisan storage:link

8. Start the development server:

php artisan serve

The API will usually be available at http://127.0.0.1:8000.

Authentication

Register or log in using the authentication endpoints. Use the authentication token returned by the API when calling protected endpoints.

Send the token in the request header:

Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json

Protected routes use Laravel Sanctum authentication.

API Endpoints

The following endpoint paths reflect the project’s current route structure. Protected endpoints require authentication unless configured otherwise.

Method	Endpoint	Description
POST	/api/register	Register a user
POST	/api/login	Log in
GET	/api/user	Get the authenticated user
GET	/api/products	List products
POST	/api/products	Create a product
GET	/api/products/{id}	Get a product
PUT/PATCH	/api/products/{id}	Update a product
DELETE	/api/products/{id}	Delete a product
GET	/api/category	List categories
POST	/api/cart/items	Add an item to the cart
POST	/api/cart	View the cart
PATCH	/api/cart/items/{cartItem}	Update cart item quantity
DELETE	/api/cart/items/{cartItem}	Remove a cart item
DELETE	/api/cart	Clear the cart
POST	/api/orders	Create an order
GET	/api/orders	List the authenticated user’s orders
GET	/api/orders/{order}	View an order
PATCH	/api/orders/{order}/status	Update order status, subject to authorization
DELETE	/api/orders/{order}	Cancel an eligible order

Check routes/api.php for the complete route list and supported HTTP methods.

Running Tests

Run the automated test suite with:

php artisan test

The feature tests cover key product, cart, and order operations, including successful requests and relevant authorization or business-rule checks.

Project Structure

app/
  Http/
    Controllers/
    Resources/
  Models/
  Policies/
routes/
  api.php
database/
  migrations/
tests/
  Feature/

Security Notes

* Keep environment secrets and database credentials out of version control.
* Use authorization rules for administrative operations.
* Validate incoming requests and enforce ownership of user-specific resources.
* Configure production environment variables separately from local development.
* Use HTTPS in production.

Author

Majd Al Najim

GitHub: @majdalnajim

Repository: laravel-ecommerce-api