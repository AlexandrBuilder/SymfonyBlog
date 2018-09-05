# SymfonyBlog

## Installation
1. Clone project: git clone https://github.com/AlexandrBuilder/SymfonyBlog.git
2. Composer update: composer update
3. Setting .evn:
* To set up doctrine/doctrine-bundle:
Example *DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name*
* To set up symfony/swiftmailer-bundle:
Example *MAILER_URL=null://localhost*
4. Create database: php bin/console doctrine:database:create
5. Load migration: php bin/console doctrine:migrations:migrate
6. Load fixtures: php bin/console doctrine:fixtures:load
7. Install encore: https://symfony.com/doc/current/frontend/encore/installation.html
8. Install yarn: yarn install
9. Start yarn: yarn encore dev
10. And at the end start the server: php bin/console server:start
