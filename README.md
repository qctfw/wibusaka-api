# WibuSaka API
Wibusaka API is a simple REST API to inform you where to watch anime legally in Indonesia using your favorite anime lists. (Currently supports MyAnimeList, and Anilist)

<p align="center">
    <a href="https://github.com/qctfw/wibusaka-api/actions"><img src="https://github.com/qctfw/wibusaka-api/actions/workflows/laravel.yml/badge.svg" /></a>
    <a href="https://github.styleci.io/repos/461522076"><img src="https://github.styleci.io/repos/461522076/shield?style=plastic" /></a>
</p>

## Tools
Primarily, this project uses Laravel as a framework. This project also uses [arm-server](https://github.com/BeeeQueue/arm-server) and [anime-offline-database](https://github.com/manami-project/anime-offline-database) for fetching anime list ID relations.

## API Documentation
Check the API Documentation **[here](https://api.wibusaka.moe/docs)**.

## Deployment
If you want to deploy for yourself, please follow these instructions.

### Requirements
- PHP 8.1+
- MySQL 5.7+
- Redis

### Installation and Configuration

1. Copy `.env.example` into `.env` and edit these values

    - Database
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=YOUR_DATABASE_NAME
    DB_USERNAME=YOUR_DATABASE_USERNAME
    DB_PASSWORD=YOUR_DATABASE_PASSWORD
    ```
    - Redis
    ```ini
    REDIS_CLIENT=predis
    REDIS_SCHEME=tcp
    REDIS_PATH=YOUR_REDIS_PATH_IF_SCHEME_IS_UNIX
    REDIS_HOST=127.0.0.1
    REDIS_PASSWORD=YOUR_REDIS_PASSWORD
    REDIS_PORT=6379
    ```

2. Install composer packages
    ```bash
    composer install
    ```

3. Generate Laravel Application Key
    ```bash
    php artisan key:generate
    ```

4. Run the Migration
    ```bash
    php artisan migrate
    ```

### Test
After installing and configuring everything, run the test to make sure the application running properly.
```bash
php artisan test
```

## Contributing

Contributions are always welcome! Create a pull request **[here](https://github.com/qctfw/wibusaka/pulls)**!

This repository used **[StyleCI](https://styleci.io)** to check the code style. You can check the rules **[here](.styleci.yml/)**.
Also, please make sure to check the existing pull request to avoid duplication.