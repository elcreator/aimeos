# Aimeos demo container

The published image bundles the Aimeos Laravel application with PHP 8.3, Apache, OPcache/JIT, and the storefront assets. The demo Compose file starts it with MySQL and MailHog and seeds the default Aimeos demo data on first launch.

```sh
docker compose -f docker-compose.demo.yml up -d
```

Open the storefront at <http://localhost:8088/en/default/shop> and the administration panel at <http://localhost:8088/admin>. The demo admin login is `admin@example.com` / `AimeosDemo2026`. You can override these with `AIMEOS_ADMIN_EMAIL` and `AIMEOS_ADMIN_PASSWORD` before starting Compose.

MailHog is available at <http://localhost:8025>. Its SMTP server receives outgoing demo mail without sending messages externally.

To stop the demo while keeping its database and uploaded files:

```sh
docker compose -f docker-compose.demo.yml down
```

To remove the demo database and files too, add `-v` to the `down` command.
