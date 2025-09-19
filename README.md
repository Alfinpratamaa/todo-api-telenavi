# Todo API (Laravel 12)

API create and get for todo app built with Laravel 12, PostgreSQL, and Pest testing.



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
cd todo-api-telenavi
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

> **Note:** Feel free to change the database configuration above according to your PostgreSQL setup (host, port, username, password, etc.).

Configure database for testing in `phpunit.xml`:

```xml
<php>
   <env name="APP_ENV" value="testing"/>
   <env name="DB_CONNECTION" value="pgsql"/>
   <env name="DB_DATABASE" value="todo_api_test"/>
   <env name="DB_USERNAME" value="postgres"/>
   <env name="DB_PASSWORD" value="postgres"/>
</php>
```

> **Note:** Update the testing database credentials to match your PostgreSQL configuration if different from the defaults above.

Create test database:

```sql
CREATE DATABASE todo_api_test;
```

5. Run migrations

before migrate makesure to changes env for database testing

```bash
php artisan migrate
```

or

```bash
php aritsan migrate --seed
```

> **Note:** You can customize the example data by modifying the factory in `database/factories/TodoFactory.php` or the seeder in `database/seeders/TodoSeeder.php`.

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

> **Note:** Tests are designed to pass with the seeded data. Ensure you run `php artisan migrate --seed` before testing and avoid modifying the factory data in `database/factories/TodoFactory.php` to maintain test consistency.

## Postman Collection

Import `public/postman-collection.json` into Postman.

or can copy paste on this :

```bash
{
    "info": {
        "_postman_id": "c1a2b3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d",
        "name": "Todo Api Telenavi",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "Create Todo",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"title\": \"Develop new chart API\",\n    \"assignee\": \"John Doe\",\n    \"due_date\": \"2025-10-15\",\n    \"priority\": \"high\",\n    \"status\": \"in_progress\",\n    \"time_tracked\": 120\n}",
                    "options": {
                        "raw": {
                            "language": "json"
                        }
                    }
                },
                "url": {
                    "raw": "{{base_url}}/todos",
                    "host": ["{{base_url}}"],
                    "path": ["todos"]
                }
            },
            "response": []
        },
        {
            "name": "Export Todos to Excel",
            "request": {
                "method": "GET",
                "header": [],
                "url": {
                    "raw": "{{base_url}}/todos/export?status=in_progress&priority=high",
                    "host": ["{{base_url}}"],
                    "path": ["todos","export"],
                    "query": [
                        {
                            "key": "status",
                            "value": "in_progress"
                        },
                        {
                            "key": "priority",
                            "value": "high"
                        }
                    ]
                }
            },
            "response": []
        },
        {
            "name": "Get Chart Data",
            "request": {
                "method": "GET",
                "header": [],
                "url": {
                    "raw": "{{base_url}}/chart?type=assignee",
                    "host": ["{{base_url}}"],
                    "path": ["chart"],
                    "query": [
                        {
                            "key": "type",
                            "value": "assignee"
                        }
                    ]
                }
            },
            "response": []
        }
    ],
    "variable": [
        {
            "key": "base_url",
            "value": "http://127.0.0.1:8000/api"
        }
    ]
}

```

Set collection variable:

-   `base_url`: `http://127.0.0.1:8000/api`

## API Usage Examples

### Create a New Todo

Send a POST request to `/todos` with the task details.

**Endpoint:** `POST /todos`

**Body:**

```json
{
    "title": "Review Final Project Documentation",
    "assignee": "Alfin Pratama",
    "due_date": "2025-10-25",
    "priority": "high",
    "status": "pending"
}
```

Returns the newly created task data.

### Export Todos to Excel

Send a GET request to `/todos/export`. You can add query parameters to filter the results.

**Endpoint:** `GET /todos/export`

**Example with filters:**

```
/todos/export?status=completed,in_progress&priority=high
```

Downloads an `.xlsx` file containing the filtered tasks and a summary row.

> **Note:** To view or download the Excel file, you can either:
>
> -   **In Postman**: Send the request, click the three dots (⋯) next to "Save Response", then select "Save response to file" to download the `.xlsx` file
> -   **In Browser**: Simply paste the URL (e.g., `http://127.0.0.1:8000/api/todos/export?status=completed`) in your browser address bar to trigger the file download

### Get Chart Data

Send a GET request to `/chart` with a type parameter to get aggregated data.

**Endpoint:** `GET /chart`

**Example to get data by assignee:**

```
/chart?type=assignee
```

Returns a JSON object with aggregated data, perfect for populating charts. Available types: `status`, `priority`, `assignee`.
