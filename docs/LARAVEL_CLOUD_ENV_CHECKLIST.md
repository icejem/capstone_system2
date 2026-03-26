# Laravel Cloud Env Checklist

This checklist is based on the current local `.env` of this project, but adjusted for Laravel Cloud production.

Do not copy the local `.env` file directly to Cloud.

## 1. Use These Values In Laravel Cloud

```env
APP_NAME="Consultation Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_FORCE_HTTPS=true

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_STORE=database
QUEUE_CONNECTION=database

BROADCAST_CONNECTION=log

FILESYSTEM_DISK=s3
PROFILE_PHOTOS_DISK=s3

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 2. Values You Should Not Copy From Local

Do not use these local development values in Laravel Cloud:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://192.168.10.4/capstone_system2L/public
APP_FORCE_HTTPS=false
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=false
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http
FILESYSTEM_DISK=local
CACHE_STORE=file
DB_USERNAME=root
```

## 3. Database Values

Do not manually copy your local database credentials if you are using a Laravel Cloud database resource.

Laravel Cloud should provide these after you attach the database:

```env
DB_CONNECTION=mysql
DB_HOST=provided-by-cloud
DB_PORT=3306
DB_DATABASE=provided-by-cloud
DB_USERNAME=provided-by-cloud
DB_PASSWORD=provided-by-cloud
```

If you want your current local data in production:

1. Export your local `consultation_db`.
2. Import it into the Laravel Cloud MySQL database.
3. Run `php artisan migrate --force` during deploy.

## 4. Object Storage Values

After attaching Object Storage in Laravel Cloud, set or confirm these:

```env
FILESYSTEM_DISK=s3
PROFILE_PHOTOS_DISK=s3
AWS_ACCESS_KEY_ID=provided-by-cloud
AWS_SECRET_ACCESS_KEY=provided-by-cloud
AWS_DEFAULT_REGION=provided-by-cloud
AWS_BUCKET=provided-by-cloud
AWS_URL=provided-by-cloud
AWS_ENDPOINT=provided-by-cloud
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Use a public bucket because the app displays profile photos using direct URLs.

## 5. Reverb / WebSockets

For the first deploy, keep Reverb disabled:

```env
BROADCAST_CONNECTION=log
```

You do not need to add these on the first deploy:

```env
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=
REVERB_PORT=
REVERB_SCHEME=
VITE_REVERB_APP_KEY=
VITE_REVERB_HOST=
VITE_REVERB_PORT=
VITE_REVERB_SCHEME=
```

The app already has a fallback for this setup.

## 6. Mail Notes

Recommended production setup:

1. Use your real production SMTP provider
2. Keep `MAIL_PORT=587` if your provider uses TLS submission
3. Keep `MAIL_ENCRYPTION=tls` unless your provider says otherwise
4. Use the sender address you want users to see
5. Use a fresh production password or API-backed SMTP credential
6. Set `MAIL_FROM_NAME="${APP_NAME}"`

If your local mail password has ever been exposed or reused, rotate it before go-live.

## 7. Build And Deploy Commands

Build command:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan view:cache
```

Deploy command:

```bash
php artisan migrate --force
```

Queue worker:

- Add a worker in Laravel Cloud if you keep `QUEUE_CONNECTION=database`.

## 8. First Deploy Order

1. Push the repo.
2. Create the app in Laravel Cloud.
3. Attach MySQL.
4. Attach Object Storage.
5. Add the environment variables from this file.
6. Add a queue worker.
7. Set the build command.
8. Set the deploy command.
9. Enable the scheduler.
10. Deploy.
11. Test login, registration, profile upload, consultation flow, and outgoing email.
