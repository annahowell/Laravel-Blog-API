# Laravel Blog API 

Simple RESTful blog API. Includes posts with tags, comments and documentation created with [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger). Role-based access control is implemented with [spatie permissions](https://github.com/spatie/laravel-permission) and [passport](https://github.com/laravel/passport).

The API can optionally be used in conjunction with my [vue-based blog frontend](https://github.com/annahowell/vue-blog-frontend).



## Table of Contents
  * [Installation](#installation)
  * [Optional Database Seeding](#optional-database-seeding)
  * [Default Role Types](#default-role-types)
  * [Making Requests](#making-requests)
    * [1. Request Headers](#request-headers)
    * [2. API Endpoints](#api-endpoints)
  * [Folder Contents](#folder-contents)
  * [Running Tests](#running-tests)



## Installation

Install dependencies with composer:

`composer install`

Copy the base env.example file and modify the .env file to reflect your database config:

`cp .env.example .env`  
`vim .env`
    
Generate the necessary database schema and seed the default commenter, editor and admin roles:
    
`php artisan migrate`

Install passport:

`php artisan passport:install`

Generate app encryption key:

`php artisan key:generate`

Generate personal access key:

`php artisan passport:client --personal`

Start the local development server:

`php artisan serve`



## Optional Database Seeding

The database can be seeded in order to generate: 
* A commenter user with the commenter role: `commenter@bar.com:Password123!`
* An editor user with the editor role: `editor@bar.com:Password123!`
* An admin user with the admin role: `admin@bar.com:Password123!`
* 20 random tags 
* 10 random posts each with up to 5 tags assigned
* 50 random comments each assigned to a user and post

Run the following commands to seed a fresh database
 
    php artisan migrate:fresh
    php artisan db:seed



## Default Role Types

By default there are three role types:

| **Role**  | **Permissions**                                                                    |
|---------- |----------------------------------------------------------------------------------- |
| Commenter | Comment                                                                            |
| Editor    | Post articles and comment                                                          |
| Admin     | Post articles, comment and manage users, tags and commenters / editors submissions |

**NOTE - The first user to signup (POST /api/v1/user) will automatically be set as the admin user.** See below for a full list of endpoints.




## Making Requests

After the installation steps above are complete, you can access the API at http://localhost:8000/api/v1, for example GET http://localhost:8000/api/v1/posts

### Request headers

| **Required** | **Key**          | **Value**             |
|------------  |----------------- |---------------------- |
| Yes          | Accept           | application/json 	  |
| Yes          | Content-Type     | application/json      |
| Optional     | Authorization    | Bearer <access_token> |


### API Endpoints

The following API endpoints are available. Full swagger documentation, including request and resource formats is available at /api/documentation after completing the installation steps above

![API Endpoints](https://github.com/annahowell/laravel-blog-api/blob/master/screenshots/1.png)




## Folder Contents

- `app` - Eloquent models
- `app/Http/Controllers` - API controllers
- `app/Http/Middleware` -  Passport auth middleware
- `app/Http/Requests` - API form requests
- `app/Http/Resources` - API form resources
- `app/Http/Policies` - API policies for user access
- `app/Http/Rules` - Custom (non-closure) request rules 
- `database/factories` - Model factory for all the models
- `database/migrations` - Database migrations
- `database/seeds` - Database seeders
- `routes/api.php` - API endpoint routes
- `tests/Feature/Http/Controllers` - API tests and dataproviders



## Running Tests

PHPUnit tests can be executed with:

    php artisan test
