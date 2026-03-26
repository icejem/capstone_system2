# Laravel Cloud Deployment Guide

This project is now prepared for a Laravel Cloud deployment with these assumptions:

- The app will use a managed MySQL database in Laravel Cloud.
- Profile photo uploads will use object storage, not the local server disk.
- Queue jobs will use the `database` driver with a worker so mail dispatches do not block web requests.
- Real-time Reverb broadcasting is optional because the app already falls back to SSE / polling for WebRTC signaling.

## 1. Before You Deploy

1. Push this repository to GitHub, GitLab, or Bitbucket.
2. Make sure your latest schema changes are in `database/migrations`.
3. If you need your existing local data, export your current MySQL database and import it into the Laravel Cloud database before going live.
4. Do not copy your current local `.env` file directly to Cloud. Create fresh production secrets instead.

## 2. Create The App In Laravel Cloud

1. In Laravel Cloud, create a new project from your Git repository.
2. Choose a PHP version compatible with this app. `composer.json` supports PHP `^8.2`, so PHP 8.2 is the safest first deploy.
3. Set the app's production branch.

## 3. Attach Required Resources

### Database

1. Add a MySQL database resource.
2. Attach it to the app environment.
3. Laravel Cloud will inject the database environment variables for you.

### Object Storage

1. Add an Object Storage resource.
2. Make the bucket public, because profile photos are rendered by direct URL in the UI.
3. Attach it to the same environment.
4. Set these environment variables in Laravel Cloud if they are not filled automatically:

```env
FILESYSTEM_DISK=s3
PROFILE_PHOTOS_DISK=s3
```

## 4. Set Environment Variables

Use the values below as your baseline production environment:

```env
APP_NAME="Consultation Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_FORCE_HTTPS=true

SESSION_DRIVER=database
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

Notes:

- Set a fresh `APP_KEY` in Laravel Cloud if it is not generated automatically.
- Add a queue worker in Laravel Cloud when using `QUEUE_CONNECTION=database`.
- Keep `BROADCAST_CONNECTION=log` unless you intentionally add a Reverb / WebSocket resource later.

## 5. Build And Deploy Commands

Recommended build command:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan view:cache
```

Recommended deploy command:

```bash
php artisan migrate --force
```

Do not add `php artisan storage:link` to the deploy command for Cloud object storage.

## 6. Enable The Scheduler

This app has a scheduled reminder command in `routes/console.php`.

1. Enable the scheduler for the app in Laravel Cloud.
2. Keep at least one running app instance so scheduled reminders can execute.

The schedule already uses `withoutOverlapping()` to reduce duplicate reminder sends.

## 7. Optional Services

### Queue Worker

Add a worker cluster for the first production deploy if you use:

```env
QUEUE_CONNECTION=database
```

or:

```env
QUEUE_CONNECTION=redis
```

### Reverb / WebSockets

Only add Reverb if you want real-time broadcasting on top of the app's existing polling fallback. If you enable it later, set:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST=...
REVERB_PORT=443
REVERB_SCHEME=https
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## 8. First Deployment Checklist

1. Create the app from your repo.
2. Attach MySQL.
3. Attach public Object Storage.
4. Add production environment variables.
5. Add a queue worker if `QUEUE_CONNECTION=database`.
6. Set the build command.
7. Set the deploy command to `php artisan migrate --force`.
8. Enable the scheduler.
9. Deploy.
10. Log in and test:
   - registration / login
   - profile photo upload
   - consultation request
   - outgoing emails
   - scheduled reminder delivery
