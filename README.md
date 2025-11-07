# API Gateway Testing Dashboard — Docker Quickstart

This project compares REST, GraphQL, and integrated gateway routes, captures CPU/memory metrics per request, and surfaces the results in a Laravel + Vite dashboard. The stack ships with a full Docker Compose environment so you can boot the entire suite (web, PHP, Node/Vite, MySQL, Redis) with a single command.

## Prerequisites

- Docker Engine 24+
- Docker Compose v2 (`docker compose` CLI)
- 4+ GB free RAM (containers: nginx, php-fpm, node, mysql, redis)

> **Ports used:** 8000 (web UI), 5173 (Vite dev server), 3306 (MySQL), 6379 (Redis). Adjust the Compose file if these clash with local services.

## 1. Clone & Environment

```bash
git clone https://github.com/TrigrD3/rest-graphql-integrated.git
cd rest-graphql-integrated
cp .env.example .env
```

Update `.env` with your GitHub token and any DB/cache settings you need. The Docker stack reads this file when the PHP container starts.

## 2. Build & Start Containers

```bash
docker compose up --build -d
```

This launches:

| Service        | Purpose                    | Default URL/Port         |
|----------------|----------------------------|--------------------------|
| `laravel-web`  | Nginx reverse proxy        | http://localhost:8000    |
| `laravel-app`  | PHP-FPM + Artisan          | internal (port 9000)     |
| `laravel-node` | Vite dev server            | http://localhost:5173    |
| `laravel-mysql`| MySQL 8.4                  | localhost:3306           |
| `laravel-redis`| Redis 7                    | localhost:6379           |

### Verify container health

```bash
docker compose ps
```

Each service should be `Up`. If MySQL or Redis is still starting, wait a few seconds before running migrations.

## 3. Install Dependencies (inside PHP container)

```bash
docker compose exec app composer install
docker compose exec node npm install
docker compose exec app bash -lc "php artisan key:generate && php artisan migrate --seed"
```



## 4. Access the Dashboard

- **Frontend (Vite dev server):** http://localhost:5173
- **Nginx proxy (serves `/public`):** http://localhost:8000

Both hit the same Laravel backend; use whichever fits your workflow. The integrated performance tester, charts, and history live on `/`.


## 5. Useful Commands

| Command | Description |
|---------|-------------|
| `docker compose logs -f laravel-app` | Tail PHP-FPM + artisan logs |
| `docker compose exec app php artisan migrate:fresh --seed` | Reset database |
| `docker compose exec node npm run build` | Compile production assets |
| `docker compose down` | Stop and remove containers |

## 7. Troubleshooting

- **Composer fails to write vendor** — ensure the project directory is owned by your user (`chown -R $USER:$USER laravel-1`).
- **MySQL connection refused** — confirm `.env` DB credentials match `docker-compose.yml` (host `laravel-mysql`, user `sail`, pass `password` by default).
- **Ports already in use** — edit `docker-compose.yml` to remap host ports (`8001:80`, etc.)
- **Assets not hot-reloading** — check the `laravel-node` container logs; restart with `docker compose restart laravel-node`.

## 8. Next Steps

- Run `php artisan test` inside the container to execute the test suite.
- Tail `storage/logs/laravel.log` via `docker compose exec app tail -f storage/logs/laravel.log` when debugging.
- Customize `.env` for production (disable debug, configure cache/queue drivers, set APP_URL, etc.)

Happy testing! 🧪🚀
