# Symfony 4.4 API Skeleton with DB-Diff-Auditor

This project is a Symfony 4.4 based skeleton for creating a RESTful API. It includes a User entity CRUD example and is pre-configured with a selection of bundles for rapid API development. It also integrates a database schema auditing tool.

## Core Technologies

*   **Framework**: Symfony 4.4
*   **Language**: PHP >=7.3.3
*   **Database**: MySQL
*   **Containerization**: Docker, Docker Compose

### Key Symfony Bundles & Libraries

*   **`friendsofsymfony/rest-bundle`**: For rapidly creating RESTful APIs.
*   **`jms/serializer-bundle`**: For flexible and powerful object serialization (JSON).
*   **`doctrine/orm`**: For database interaction and object-relational mapping.
*   **`doctrine/doctrine-migrations-bundle`**: For managing database schema migrations.
*   **`whatafunc/db-diff-auditor`**: A development tool for snapshotting and comparing database schemas.

## Developed Features

A simple RESTful API for managing `User` entities has been implemented.

*   **Entity**: `App\Entity\User` with fields: `id`, `username`, `email`.
*   **Controller**: `App\Controller\Api\UserController`.
*   **Endpoints**:
    *   `POST /api/users`: Creates a new user.
        *   **Body**: `{"username": "someuser", "email": "user@example.com"}`
    *   `GET /api/users`: Lists all existing users.

## Testing & Performance Analysis

The API endpoints were manually tested for functionality and performance using `curl`.

### Functional Testing

*   Verified that `POST /api/users` successfully creates new users in the database.
*   Verified that `GET /api/users` retrieves a JSON array of all created users.

### Performance Profiling

*   **Initial State**: The API initially had a very slow response time, with a Time To First Byte (TTFB) of over **1.1 seconds** in the local development environment.
*   **Optimization Applied**: To improve performance, several steps were taken:
    *   The database host was switched from `localhost` to the direct IP address of the containerized DB server to reduce DNS/network lookup overhead.
    *   The Composer autoloader was optimized using the following command:
    ```bash
    composer dump-autoload -o --classmap-authoritative
    ```
*   **Result**: After optimization, the TTFB was significantly reduced. The best-case result using `localhost` (bypassing potential local DNS overhead) showed a TTFB of **~187ms**, a major improvement from the initial 1.1s:
    ```
    $ curl -o /dev/null -s -w 'DNS: %{time_namelookup}s | Connect: %{time_connect}s | TTFB: %{time_starttransfer}s | Total: %{time_total}s\n' http://localhost:8081/api/users
    DNS: 0.007705s | Connect: 0.008173s | TTFB: 0.186687s | Total: 0.187663s
    ```
*   **Further Recommendations**: For production-like performance, the following steps are recommended:
    1.  Ensure the application is running in the `prod` environment (`APP_ENV=prod` in `.env`).
    2.  Warm up the Symfony cache: `php bin/console cache:warmup --env=prod`.
    3.  Use persistent database connections by adding `?persistent=1` to the `DATABASE_URL` in your `.env` file.

## Database Management & Auditing

Database schema is managed via Doctrine ORM and migrations.

Additionally, this project uses `whatafunc/db-diff-auditor` to track schema changes outside of the formal migration process. This is useful for auditing and ensuring environments are in sync.

### DB Diff Auditor Usage

The following commands were used to track the creation of the `users` table:

1.  **Create a snapshot**:
    ```bash
    vendor/bin/db-diff db:check
    ```
2.  **Compare the last two snapshots**:
    ```bash
    vendor/bin/db-diff db:compare-last
    ```
This confirmed that the `users` table, with its columns and keys, was correctly created as the main schema difference between snapshots.