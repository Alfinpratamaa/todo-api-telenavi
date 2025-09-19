# Todo API (Laravel 12)

RESTful API for task management built with Laravel 12, PostgreSQL, and Pest testing.

## Features

-   Create new tasks with input validation
-   Chart data aggregation by status, priority, assignee
-   Excel export with dynamic filters and summary rows

## Requirements

-   PHP >= 8.2
-   Composer
-   PostgreSQL

## Installation

1. Clone repository

```bash
git clone https://github.com/Alfinpratamaa/todo-api-telenavi.git
cd todo-api
```

2. Install dependencies

```bash
composer install
```

3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure PostgreSQL database
   Create database:

```sql
CREATE DATABASE todo_api;
```

Update `.env`:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=todo_api
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

5. Run migrations

```bash
php artisan migrate
```

## Usage

Start development server:

```bash
php artisan serve
```

API available at: `http://127.0.0.1:8000`

## Testing

Run test suite:

```bash
php artisan test
```

## Postman Collection

Import `public/postman-collection.json` into Postman.

Set collection variable:

-   `base_url`: `http://127.0.0.1:8000/api`
