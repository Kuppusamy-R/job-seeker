Steps for without importing myjob.sql
    1. Migration & Seeding
        php artisan migrate
        php artisan db:seed
        php artisan db:seed --class=AdminSeeder
        php artisan db:seed --class=SkillSeeder

    2. PHP Process Commands
        php artisan serve
        php queue:work

    3. Admin Credential
        Admin Login: localhost:8000/admin/login
        Admin Username: kuppusamy433@gmail.com
        Password: password

Steps for with importing myjob.sql
    1. PHP Process Commands
            php artisan serve
            php queue:work

    2. Admin Credential
        Admin Login: localhost:8000/admin/login
        Admin Username: kuppusamy433@gmail.com
        Password: password