
# Travel Orders

## Overview
**Travel Orders** is a web application designed to manage and streamline travel orders. Built with Laravel and Docker, it provides a seamless development and deployment experience.

## Technologies Used
- **Framework:** Laravel v11.35.1
- **Containerization:** Docker
- **Database:** MySQL

## Requirements
- **PHP:** v8.2.26
- **MySQL:** Latest version supported by Laravel

---

## Installation Guide

Follow these steps to set up the project on your local machine:

### 1. Start Docker Containers
1. Open a terminal in the project directory (where the `docker-compose.yml` file is located).
2. Run the following command to start the containers:
   ```bash
   docker-compose up -d
   ```

### 2. Access the Application Container
Once the containers are running, access the application container:
   ```bash
   docker exec -it onf-app bash
   ```

### 3. Install Composer Dependencies
Inside the application container, install the required Composer dependencies:
   ```bash
   composer install
   ```

### 4. Set Up Environment File
1. Create the `.env` file using the example provided:
   ```bash
   cp .env.example .env
   ```

2. Generate the application key:
   ```bash
   php artisan key:generate
   ```

### 5. Configure JWT
#### If JWT is already installed:
Generate the JWT secret:
   ```bash
   php artisan jwt:secret
   ```

#### If JWT is not installed:
1. Install the JWT package:
   ```bash
   composer require tymon/jwt-auth
   ```
2. Publish the JWT configuration:
   ```bash
   php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
   ```
3. Generate the JWT secret:
   ```bash
   php artisan jwt:secret
   ```

### 6. Run Migrations
With the environment file configured, execute the database migrations:
   ```bash
   php artisan migrate
   ```

---

## Running Tests

Follow these steps to execute the tests:

1. Create a `.env.testing` file:
   ```bash
   cp .env .env.testing
   ```

2. Configure the test database connections in `.env.testing`. Alternatively, you can run the tests on the same database used for development (not recommended). Ensure the test databases are empty before running tests.

3. Run the tests using Artisan:
   ```bash
   php artisan test
   ```

   Or directly with PHPUnit:
   ```bash
   vendor/bin/phpunit
   ```

---

## User Creation

To create a default user, use the following command:
   ```bash
   php artisan db:seed --class=UserSeeder
   ```

Default user credentials:
- **Email:** `admin@example.com`
- **Password:** `password1234`

---

## Application Paths

- **Application URL:** [http://localhost/](http://localhost/)
- **phpMyAdmin (OnFly):** [http://localhost:8081/](http://localhost:8081/)
- **phpMyAdmin (Travel Orders):** [http://localhost:8082/](http://localhost:8082/)

---

## Endpoints

> **Note:** Replace `TOKEN-HERE` in the requests below with the token received from the first endpoint (Retrieve Token).

### 1. Retrieve Token
   ```bash
   curl --location 'http://localhost/api/login' \
   --header 'Content-Type: application/json' \
   --data-raw '{
      "email": "admin@example.com",
      "password": "password1234"
   }'
   ```

### 2. Register a Travel Order
   ```bash
   curl --location 'http://localhost/api/travel-orders' \
   --header 'Content-Type: application/json' \
   --header 'Authorization: Bearer TOKEN-HERE' \
   --data '{
      "requester_name": "Pedro Alves",
      "destination": "Londres",
      "departure_date": "2025-01-05",
      "return_date": "2025-01-17"
   }'
   ```

### 3. Update Travel Order Status
   ```bash
   curl --location --request PUT 'http://localhost/api/travel-orders/1' \
   --header 'Content-Type: application/json' \
   --header 'Authorization: Bearer TOKEN-HERE' \
   --data '{
      "status": "approved"
   }'
   ```

### 4. Get Travel Order by ID
   ```bash
   curl --location 'http://localhost/api/travel-orders/1' \
   --header 'Authorization: Bearer TOKEN-HERE'
   ```

### 5. Get All Travel Orders
   ```bash
   curl --location 'http://localhost/api/travel-orders' \
   --header 'Authorization: Bearer TOKEN-HERE'
   ```

### 6. Notify a Travel Order
   ```bash
   curl --location --request POST 'http://localhost/api/travel-orders/1/notify' \
   --header 'Authorization: Bearer TOKEN-HERE'
   ```

---

## Additional Resources

For more information, please refer to the [Laravel 11 Documentation](https://laravel.com/docs/11.x).
