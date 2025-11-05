# Guestbook demo (PHP 8 + MariaDB) with GitHub Actions SSH deploy

This is a minimal demo app that runs a PHP 8 guestbook backed by MariaDB. The GitHub Actions workflow will copy the application files to a remote Docker host over SSH and run docker compose there.

Files added
- `app/` – PHP app and `Dockerfile`
- `docker-compose.yml` – defines app and MariaDB service
- `.github/workflows/deploy.yml` – GitHub Actions workflow to copy files to remote host and run `docker compose up -d --build`

Secrets required in your repository settings
- `SSH_HOST` – remote host IP or domain
- `SSH_USER` – SSH username (must have permission to run docker)
- `SSH_PORT` – SSH port (usually `22`)
- `SSH_PRIVATE_KEY` – private key (no passphrase recommended for CI)
- `REMOTE_PATH` – absolute path on the remote host where files will be copied (e.g. `/home/ubuntu/guestbook`)

Remote host requirements
- Docker and Docker Compose (or Docker Compose V2 as `docker compose`) must be installed.
- The SSH user must be able to run Docker (be in `docker` group or use sudo without password for docker commands).

Testing locally
1. Build and run with Docker Compose locally:

```bash
docker compose up -d --build
# Open http://localhost:8080
```

2. To test deployment from GitHub Actions, push to `main` after you configure the secrets above.

Security note
- The workflow copies your app and `docker-compose.yml` to the remote host. Keep your secrets out of the repository and use GitHub Encrypted Secrets.

That's it — a compact demo for deploying a PHP 8 + MariaDB stack to a remote Docker host via SSH.
