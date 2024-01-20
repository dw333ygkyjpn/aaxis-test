# AAXIS Symfony Test

This is a Symfony test project created for the hiring process at AAXIS.

## Technologies Used

- Symfony 7.0
- PHP >=8.2 with Xdebug 3
- PostgresQL 16-alpine
- Docker Compose
- Symfony Stimulus

![current-stack]()

## Requirements

To run this project, make sure you have the following installed:

- PHP >=8.2 with Xdebug 3
- [Symfony CLI](https://symfony.com/download) (latest version)
- [Docker Compose](https://docs.docker.com/compose/install/) >= v2.10
- [GNU Make](https://gnuwin32.sourceforge.net/packages/make.htm) (only for Windows)

## Run the Project

1. Clone the repository:

    ```bash
    git clone https://github.com/dw333ygkyjpn/aaxis-test.git
    ```

2. Check if the Symfony requirements are met:

    ```bash
    symfony check:requirements
    ```

3. Run the following command to build the application:

    ```bash
    make build
    ```

The application will be accessible at [https://localhost:8000/](https://localhost:8000/).

---

## Application overview

### Docker Setup

The Docker container is configured with Compose and includes the database and Adminer, accessible at [http://localhost:8080/](http://localhost:8080/).

### Task Runner

A Makefile script is included, providing convenient shortcuts for a better developer experience.

![Screenshot of Makefile Help]

To view available commands, run:

```bash
make help
```
![current-make-help]()

### Test
You can run all the phpunit test using:

```bash
make test
```

The test are located in the `/test` folder.

The project comes with a [script](https://ocramius.github.io/blog/automated-code-coverage-check-for-github-pull-requests-with-travis/) to check the tests coverage, you can check the coverage report with:

```bash
make coverage
```

This will run the test and calculate the global code coverage by parsing and analysing the output of the Clover 
report in the clover.xml, you can see this report in HTML format running:

```bash
make cov-report
```

### Coding Standards
**You can run all the coding standards checks with:**
```bash
make cs
```

#### Static analysis
The project utilizes PHPStan to scan for any possible bugs, to execute the analysis, run:
```bash
make stan
```
The current output of the analysis:

![current-stan]()

#### CS Rules
The project utilizes PHP-cs-fixer to follow the symfony standards and some common php standards
The current ruleset:

![php-cs-ruleset]()

#### Linting
You can check if the code follows the symfony best practices with the symfony lint commands, the make file has some
shortcuts to run these checks

![]()
`make lint` runs all the above commands

#### CI
The project has a Github workflow that runs the CS, tests and lints commands, the main branch has a rule to allow 
pull request only with these checks passed, you can see the workflow status in the [Actions](https://github.com/dw333ygkyjpn/aaxis-test/actions) page.


## Considerations

The `make build` command, specifically `make start`, will use `sudo` to run `symfony serve -d`. This is necessary for macOS and other environments where Docker environment variables are not exposed by default.

If your system exposes Docker environment variables without issues, you can remove the `sudo` command.

I used the [strangebuzz/MicroSymfony](https://github.com/strangebuzz/MicroSymfony) template to bootstrap the project, 
i decided to use this symfony skeleton template because its a fast and easy way to start a project with some 
common default libraries and scripts that modern symfony applications use
