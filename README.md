# CMS Hubspot Application

## Prerequisites

-   **PHP**: ^8.2
-   **Composer**: Latest stable version
-   **Node.js & npm**

## Installation:

-   ```bash
    composer install
    ```

-   ```bash
    npm install
    ```

-   ```bash
    php artisan storage:link
    ```
-   Go to php.ini and search for extension=gd, then uncomment it.
-   Run the command: composer update (to make sure the package installed)
-   Copy the .env.example to .env file:

-   ```bash
      cp .env.example .env
    ```

## Run Application:

-   ```bash
      php artisan serve
    ```
