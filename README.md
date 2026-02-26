## About Quiz Score

1. Laravel
2. Vue JS
3. Composition API - Vue JS
4. Tailwind CSS
5. Vite
6. SQLite/MySQL -- (set via .env)
7. PHP ^8.2 (must be installed)

## Getting Started Step by Step setup

folder using command
   `cd quiz-score`

3. Then install required libraries using (PHP 8.2 required)
   `composer install`

4. Then create a .env file and generate key for this project using command
   `cp .env.example .env`

`php artisan key:generate`

5. Setup MYSQL 

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_score
DB_USERNAME=root
DB_PASSWORD=
```

6. After connecting the db with project, then run command
   If you use sqlite, then you don't need to run command

```
php artisan migrate:fresh --seed
```

7. Running migrate and db-seed gives you 1 admin account to manage quiz and question and 2 student account to attend quiz

```
admin credentials
email: admin@test.com
pass:  1234
```

```
studen credentials
email: student@test.com
pass:  1234
```

7. Then compile all CSS & JS files together using this command

```
npm install 

npm run dev
```

8. Then open another terminal and run this command

```
php artisan serve
```
