# AAXIS Symfony Test
Symfony test project for the hiring process at AAXIS

## Technologies used
- Symfony 7.0
- PHP >=8.2
- Docker compose
- Symfony Stimulus

## Run the project
- [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
- run `make start` or:

1. `docker compose build --no-cache`
2. `docker compose up -d`
3. `symfony serve -d`

The application will run @ https://localhost:8000/

If the symfony server binary does not uses the docker env variables, create a .env.local file with this config:
`DATABASE_URL="postgres://app:sd2f3jk23@127.0.0.1:51435/app?sslmode=disable&charset=utf8"`