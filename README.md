
# Arquivei's challenge - 'Boltons challenge'
This repo solves the arquivei's challenge of scraping it's sandbox API, saving in a MariaDB database and exposing through a REST JSON web API using PHP.

## Pre-requirements
* docker
* docker-compose

## Instalation and usage

Create a file named `.env` in the root of this project and set the environment variables: `API_ID`,`API_KEY` and `MARIADB_ROOT_PASSWORD`, example: 
```
API_ID=YOUR_API_ID
API_KEY=YOUR_API_KEY
MARIADB_ROOT_PASSWORD=root
```
After that, run `docker-compose up --build` to build and start all services, it will expose your port 8000 and you can access each NFe by using 
the following endpoint `/api/nfe/{ACCESS KEY}`, example: `http://localhost:8000/api/nfe/123`.

Initially the project will start without any data in the database, you can run `docker-compose run app php src/scraper.php`, to populate/update the database with the required data, optionally you can add a crontab to execute the scraper periodically, example (run each 10 minutes):
```
*/10 * * * * cd ABSOLUTE_PATH_TO_THIS_PROJECT && docker-compose run app php src/scraper.php
```

## Runnig unit tests

Run `docker-compose run app vendor/bin/phpunit tests`

## How it works
This solutions is split into two main programs, the web API that exposes the scraped data from the arquivei's sandbox API and the scraper that does the actual scraping and saves into the database, both share the same MariaDB database that contains
just a single table named `nfe` that holds each nfe access key and total value.

The project uses Docker to create isolate environments for the PHP runtime and MariaDB server, so that it increases its reproducibility and makes 
it easy to setup the project. This solution also uses composer to easily use third party libraries like php requests and the slim framework.

### Scraper
The scraper iterates through all the sandbox JSON API by using the `res.page.next` response until `res.data` length is not equal to the default limit of 50 per request, for each entry in `res.data` it collects each access key and parses the bas64 -> XML NFe information, by using [taxpayer integration manual](https://www.nfe.fazenda.gov.br/portal/exibirArquivo.aspx?conteudo=zxlLdxB/oYA=) as reference it parses the XML and search for 
the `vNF` tag, that holds the NFe's total value.

### Web API
The API displays each nfe by using the endpoint `/api/nfe/{ACCESS KEY}`, returning an error if no NFe is found, or a database error has occurred, 
as well as returning 404 in undefined routes.