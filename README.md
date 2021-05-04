# booklibrary (with Easy Admin)

Example project that uses:
  - Symfony 5
  - Doctrine (Sqlite DB)
  - Cache (Redis)
  - Encore (frontend with tailwindcss configuration & alpineJS)
  - Easy Admin 3 (backend)
  - Auth
  - User Roles
  - Email
  - Events & Listeners
  - etc.

## how to install

```bash
# run 
git clone https://github.com/n1crack/booklibrary_easyadmin.git <folder_name>

```

```bash
# run 
composer install
```

```
# login path: /login

username: admin
password: pass

# register path: /register


# admin path: /admin
```

```env
# set MAILER_DSN variable in your ".env" file:
# you can use https://mailtrap.io/ for testing
MAILER_DSN="smtp://<your-unique-hash-id>@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login"

# set DATABASE_URL variable in your ".env" file:
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```
