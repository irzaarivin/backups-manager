# Docker Deployment

## 1. Prepare the VPS

The host backup directory must exist:

```bash
sudo mkdir -p /home/claude/services/backups/sqls
```

The Docker container only needs read permission on that directory.

## 2. Configure environment

```bash
cp .env.example .env
```

Set production values in `.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
APP_PORT=8080
APP_KEY=base64:generate-a-stable-key-before-deploying

BACKUP_SOURCE_PATH=/home/claude/services/backups/sqls
BACKUP_LOGIN_USERNAME=your-admin-username
BACKUP_LOGIN_PASSWORD=use-a-long-random-password
```

Generate an application key before deployment:

```bash
docker run --rm -v "$PWD":/app -w /app composer:2 php artisan key:generate --show
```

Put the returned value into `APP_KEY`.

## 3. Build and start

```bash
docker compose up -d --build
```

The application will be available at:

```text
http://your-server-ip:8080
```

## 4. Check status and logs

```bash
docker compose ps
docker compose logs -f app
```

The health endpoint is:

```text
http://your-server-ip:8080/up
```

## 5. Reverse proxy and HTTPS

Put Nginx, Caddy, or a managed proxy in front of port `8080`. Only expose the proxy ports `80` and `443` publicly. Keep the backup directory outside the repository and do not copy it into the Docker image.

## Notes

- The backup mount is read-only: `/home/claude/services/backups/sqls:/backups:ro`.
- Laravel's SQLite database, sessions, cache, and logs are stored in the `laravel_storage` Docker volume.
- The entrypoint runs migrations and seeds the configured login user on container startup.
- If the host path differs, change `BACKUP_SOURCE_PATH` in `.env`; `BACKUP_ROOT` inside the container remains `/backups`.
