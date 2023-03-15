
# Surplus Backend Challenge

RESTful API build with Laravel 10 + Docker


# How To Run
- Make sure you have [Docker](https://www.docker.com/) installed.
- Open your command line in the root project's directory and type the following command.

```sh
$ docker-compose up -d
```
this will take awhile to download all the necessary images needed and to create all the containers.

- After finished with **docker-compose**. It will create 4 containers (**surplus-app**, **surplus-nginx**, **surplus-mysql**, **surplus-adminer**). We'll run all the composer and artisan command inside **surplus-app** container. To access the container, type the following command on your command line.
```sh
$ docker exec -it surplus-app /bin/bash
```
- After accessing the container, we need to set the .env file first. Run this following command to copy .env.example content to .env.
```sh
$ cp .env.example .env
```
- To generate the application key, we first need to install all the laravel package and then generate the key.
```sh
$ composer install
```
```sh
$ php artisan key:generate
```
- And then set DB connection in .env follows:
```php
DB_CONNECTION=mysql
DB_HOST=surplus-mysql
DB_PORT=3306
DB_DATABASE=surplus
DB_USERNAME=root
DB_PASSWORD=secret
```
- We can run all this following commands on the container's terminal to run the project.

```sh
$ php artisan migrate
```
```sh
$ php artisan passport:install
```
```sh
$ php artisan db:seed
```
If all the command is successfully running then the program is ready to use.

# Using APIs

To use the APIs, you have to set two header follows:
```php
#Call login or register apis put $accessToken. 

'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Bearer '.$accessToken,
 ] 
```
For login & register endpoints, no need to set the Authorization header. Just set the Accept header.
