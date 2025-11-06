# Guestbook Demo Application with Automated Deployment

This guide explains how to run and deploy a PHP 8 + MariaDB application using Docker and GitHub Actions. It's designed for developers who are new to Docker, CI/CD, and automated deployments.

## What's Inside This Project?

This project demonstrates a simple but complete web application setup with:
- A PHP 8 web application (a guestbook)
- A MariaDB database for data storage
- Automated deployment using GitHub Actions
- Docker containers for consistent development and production environments

### Key Files Explained

1. `app/` directory:
   - Contains the PHP application code
   - Includes a `Dockerfile` that defines how to build the PHP application container

2. `docker-compose.yml`:
   - This is the main configuration file that defines our application's services
   - It sets up two services:
     - `app`: The PHP web application (runs on port 8080)
     - `db`: The MariaDB database
   - Manages the connections between services
   - Handles environment variables for database configuration
   - Sets up persistent storage for the database

3. `.github/workflows/deploy.yml`:
   - This is our automated deployment configuration
   - Triggers automatically when code is pushed to the `main` branch
   - Securely copies files to your production server
   - Runs Docker commands to update your application

## Understanding docker-compose.yml

Our `docker-compose.yml` file is structured to make development and deployment easy:

```yaml
version: '3.8'  # Docker Compose version we're using
services:
  app:  # Our PHP application
    build: ./app  # Builds using the Dockerfile in the app directory
    ports:
      - "8080:80"  # Maps port 8080 on your computer to port 80 in the container
    environment:  # Database connection settings
      DB_HOST: db
      DB_NAME: guestbook
      DB_USER: guest
      DB_PASS: guestpass
    depends_on:
      - db  # Ensures database starts before the app

  db:  # Our MariaDB database
    image: mariadb:10.6  # Uses official MariaDB image
    restart: always  # Automatically restarts if it crashes
    environment:  # Database configuration
      MYSQL_ROOT_PASSWORD: rootpass
      MYSQL_DATABASE: guestbook
      MYSQL_USER: guest
      MYSQL_PASSWORD: guestpass
    volumes:
      - db_data:/var/lib/mysql  # Persistent storage for database files

volumes:
  db_data:  # Defines a persistent volume for database data
```

## Understanding the Deployment Process (deploy.yml)

Our GitHub Actions workflow automates the deployment process. Here's how it works:

1. **Trigger**: 
   - The deployment starts automatically when you push code to the `main` branch

2. **Environment**:
   - Uses Ubuntu for running the deployment tasks

3. **Steps**:
   a. Checks out your code
   b. Copies files to your server using SCP (Secure Copy)
   c. Connects to your server via SSH and:
      - Updates Docker containers
      - Rebuilds the application
      - Cleans up old Docker resources

## Setting Up for Deployment

### 1. Required GitHub Secrets

You need to add these secrets in your GitHub repository settings (Settings → Secrets and variables → Actions):

- `SSH_HOST`: Your server's IP address or domain name
- `SSH_USER`: Username for SSH login
- `SSH_PORT`: Usually `22` (standard SSH port)
- `SSH_PRIVATE_KEY`: Your SSH private key (without passphrase)
- `REMOTE_PATH`: Where to put files on your server (e.g., `/home/ubuntu/guestbook`)

### 2. Server Requirements

Your production server needs:
1. Docker installed
2. Docker Compose V2 installed
3. A user that can run Docker commands (either in the `docker` group or with sudo access)

## Local Development

To run the application on your local machine:

1. Install Docker and Docker Compose on your computer
2. Open a terminal in the project directory
3. Run:
   ```bash
   docker compose up -d --build
   ```
4. Visit http://localhost:8080 in your web browser

The `-d` flag runs containers in the background, and `--build` ensures your application is rebuilt with any changes.

## Security Considerations

1. Never commit sensitive information to your repository
2. Use GitHub's encrypted secrets for sensitive data
3. Keep your SSH keys secure and never share them
4. Regularly update your Docker images and dependencies

## Troubleshooting

If you encounter issues:

1. Check the Docker logs:
   ```bash
   docker compose logs
   ```

2. Ensure all GitHub secrets are correctly set

3. Verify your server's Docker installation:
   ```bash
   docker --version
   docker compose version
   ```

4. Check server permissions:
   ```bash
   groups  # Should show 'docker' if properly configured
   ```

## Getting Help

If you need assistance:
1. Check Docker's official documentation: https://docs.docker.com
2. Review GitHub Actions documentation: https://docs.github.com/actions
3. Open an issue in this repository for project-specific questions

---

This project demonstrates a practical implementation of modern deployment practices. While it's a simple guestbook application, the same principles apply to more complex applications.
