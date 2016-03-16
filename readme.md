## CEP

### Prerequisites

In order to run the application you need to setup server with:

- PHP 7+
- MySQL 5.7+

For development process, you should use [Laravel Homestead](https://laravel.com/docs/5.2/homestead)

### Coding standards

In order to follow code standards, you should use PSR-2, install [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and before each commit you should run this fixer to make sure your code is properly formatted. Base CS fixer configuration file has created as `.php_cs` to make sure every developer uses the same rules for code formatting.   


### Installation

1. Copy `.env.example` as `.env`

2. Run `composer install`

3. In `.env` file:

    - Set `APP_KEY` to random 32 characters long string using the following command:
    
    ```
    php artisan key:generate
    ```
    
    - If you set `SQL_LOG_QUERIES` or `SQL_LOG_SLOW_QUERIES` to true (to log SQL queries), make sure you have created directory set as `SQL_LOG_DIRECTORY` in storage path and you have valid permissions to create and override files in this directory
        
    - Fill in all other data in `.env` file (database connection admin e-mail and password and so on)        

4. Run    

    ```
    php artisan jwt:generate --show
    ```
   
    and put key you got into .`env` file as `JWT_SECRET` value
    
5. Run
 
    ```
    php artisan migrate --seed 
    ```
    
    to run migrations and seeds into database  

### Running tests

In order to run `phpunit` tests you should prepare a few things.

1. You need to create separate database connection and set it in `.env` file in `TESTING` section
 
2. Run `php artisan migrate --seed --database=mysql_testing` to run all migrations and seeds into this testing database (don't change `mysql_testing` when running into this command - it's not the database name!)

3. You should make sure that in your `.env` file `MAIL_HOST` is set to `mailtrap.io` and all e-mail settings are filled correctly

4. Now you need to fill in `MAILTRAP_API_TOKEN` and `MAILTRAP_API_INBOX` based on your mailtrap account. To do that, you should log in into your `mailtrap.io` account, and go to `https://mailtrap.io/public_api` where you have your API key. To get your API inbox you need to go to `https://mailtrap.io/inboxes` and when you click your inbox just use id that you will find in url.
