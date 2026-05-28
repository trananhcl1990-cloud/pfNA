# pfNA CMS

Laravel portfolio CMS for pfNA.

## Local Domain

Use this project with:

```text
http://pfna.local
```

Add this line to the Windows hosts file:

```text
127.0.0.1 pfna.local
```

Hosts file path:

```text
C:\Windows\System32\drivers\etc\hosts
```

Then run the project:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan serve --host=pfna.local --port=8000
```

Admin:

```text
http://pfna.local:8000/admin
```

Default login:

```text
admin@pfna.local
admin123456
```

CMS data is stored in `storage/app/cms`, so it can run without a database driver.
