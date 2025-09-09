# WordPress Docker Template

Spin up a production‑like WordPress stack locally with Docker: MySQL 8, WordPress (PHP‑FPM + Apache inside the official image), and Nginx as a reverse proxy. Includes sensible PHP and Nginx defaults, persistent volumes, and an additional WordPress config file.

## Features
- MySQL 8 database with persistent storage (./dbdata)
- WordPress container with mounted wp-content for easy development
- Nginx reverse proxy on ports 80/443
- Custom PHP configuration via php.ini (upload limits, memory, timeouts)
- Additional WordPress constants via additional-config/additional-config.php
- Uses .env for secrets and DB credentials

## Prerequisites
- Docker and Docker Compose (v2)
- Ports 80 and 443 available locally

## Quick start
1. Create a .env file in the project root (same folder as docker-compose.yml):

   ```env
   # Database
   MYSQL_ROOT_PASSWORD=changeme-root
   MYSQL_DATABASE=wordpress
   MYSQL_USER=wp
   MYSQL_PASSWORD=changeme-wp
   ```

2. Start the stack:

   ```bash
   docker compose up -d
   ```

3. Open WordPress at:
- http://localhost

4. Complete the WordPress installer. Use these DB settings if prompted:
- Database name: wordpress
- Username: value of MYSQL_USER from .env
- Password: value of MYSQL_PASSWORD from .env
- Database host: db:3306

To stop the stack:

```bash
docker compose down
```

## Project structure
- docker-compose.yml — defines services: db (mysql:8.0), wordpress (official image), nginx (alpine)
- php.ini — custom PHP settings (mounted into the WordPress container)
- additional-config/additional-config.php — extra WordPress constants (mounted via WORDPRESS_CONFIG_EXTRA)
- nginx/
  - nginx.conf — global Nginx config
  - default.conf — server config (proxies to the wordpress container)
- wp-content/ — mapped to /var/www/html/wp-content (themes, plugins, uploads)
- dbdata/ — MySQL data directory for persistence

## Configuration notes
- WordPress URLs: additional-config sets
  - WP_SITEURL = http://localhost
  - WP_HOME = http://localhost
  Adjust if you run behind a different host or domain.
- PHP limits (php.ini):
  - upload_max_filesize = 500M
  - post_max_size = 530M
  - memory_limit = 600M
  - max_execution_time = 300
- Nginx listens on 80 and 443 (no certs by default). HTTPS server block is present but commented; add your certs and enable when needed.

## Common commands
- Start: docker compose up -d
- Stop: docker compose down
- View logs (all): docker compose logs -f
- Logs (nginx/wordpress/db): docker compose logs -f nginx|wordpress|db
- Recreate after changes: docker compose up -d --build

## Data persistence and volumes
- ./dbdata -> /var/lib/mysql (database data)
- ./wp-content -> /var/www/html/wp-content (themes, plugins, uploads)
- ./additional-config -> /var/www/additional-config
- ./php.ini -> /usr/local/etc/php/conf.d/custom.ini

### Managing wp-content (first-time setup)
You'll note that the final volume is the wp-content one. This will replace the entire wp-content folder inside WordPress. In docker-compose.yml it's commented out initially because we first want WordPress to generate the default contents (themes, plugins, uploads structure) inside the container. Then we copy that to the host and enable the bind mount.

Steps:
1) Start the stack and let WordPress initialize:
   
   ```bash
   docker compose up -d
   ```

2) With the containers running, copy wp-content from the container to your project root:
   
   ```bash
   docker cp wordpress:/var/www/html/wp-content .
   ```

3) Stop the stack:
   
   ```bash
   docker compose down
   ```

4) Edit docker-compose.yml and uncomment the wp-content volume mapping under the wordpress service:
   
   ```yaml
   volumes:
     - ./additional-config:/var/www/additional-config
     - ./php.ini:/usr/local/etc/php/conf.d/custom.ini
     - ./wp-content:/var/www/html/wp-content  # uncomment this line
   ```

5) Commit your WordPress setup to version control:
   
   ```bash
   git add .
   git commit -m "Add wp-content from container and enable bind mount"
   ```

Why commit wp-content? You'll be editing themes and plugins (adding, deleting, or modifying). Keeping wp-content in Git makes sense so your development changes are tracked.

Backing up your site:
- Database: use mysqldump inside the db container or back up ./dbdata (cold backups recommended)
- Files: back up ./wp-content

## Environment variables
From docker-compose.yml and .env:
- WORDPRESS_DB_HOST=db:3306
- WORDPRESS_DB_USER=$MYSQL_USER
- WORDPRESS_DB_PASSWORD=$MYSQL_PASSWORD
- WORDPRESS_DB_NAME=wordpress
- WORDPRESS_CONFIG_EXTRA=require '/var/www/additional-config/additional-config.php';

You must supply at least MYSQL_ROOT_PASSWORD, MYSQL_USER, and MYSQL_PASSWORD in .env.

## Troubleshooting
- Port already in use: Stop other services on 80/443 or change nginx port mapping in docker-compose.yml
- Can’t connect to DB: Ensure .env has matching MYSQL_* values and containers are healthy: docker compose ps
- Upload limits: php.ini and nginx configs already raise limits; restart containers after changes
- Reset environment: docker compose down -v (WARNING: removes volumes, including DB data)

## License
If this template is part of another project, refer to that project’s license. Otherwise you may apply your own license.
