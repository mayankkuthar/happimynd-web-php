# happimynd

# Setup Instructions


## Installation with Dump SQL
- Clone the repository(main branch).
- `cd happimynd`
- add `.env` file into the project. Get the latest .env file from stagging server.
- Create database.
- Get the sql dump from stagging server.
    - login to stagging server.
    - To create sql dump run. 
    `sudo mysqldump -u root -p happimynd_2 > <dump_name>.sql`
    - download dump sql.
- Import dump sql database into local database.
    - `sudo mysql -u root -p <database_name> < <dump_name>.sql`
- Run
    - `composer install`
    - `php artisan jwt:secret`
    - `php artisan serve`


## Fresh Installation
clone repository and open that project folder

```bash
cd happimynd
```
Download env from [Here](https://drive.google.com/file/d/16uAqlXRwwYrf0zzCuJ7A5g8PYPCSyS8h/view?usp=sharing)

after downloading place .env file inside happimyndPWA/happimynd/

Create database with name happimynd

```bash
composer install
php artisan jwt:secret
php artisan migrate --seed
```

useful commands
```bash
php artisan migrate:fresh --seed =>to run fresh migration(drop and create tables) and then seed
php artisan db:seed =>to seed database with random data
```

### Default credentials:
##### Admin Panel:

```
superadmin@happimynd.com
admin@happimynd.com
content-writer@happimynd.com

password for all above emails: password
```

##### user Login
```
username: happimynd
password: password
```
##### Packages/Libraries/Templates used
```
https://spatie.be/docs/laravel-permission/v3/introduction
https://github.com/ColorlibHQ/gentelella
```



