## [Total Energies Startups](https://startupper.totalenergies.com/en) Scrapping

This PHP project currently scrapes only project from West Africa.

Using [symfony panther](https://github.com/symfony/panther) package as proof of concept for scrapping

## Setup

```bash
git clone https://github.com/gausoft/total-energies-projects-scrapping.git
```

```
cd total-energies-projects-scrapping/
```

```bash
composer install
```

## Install a headless browser

```bash
composer require --dev dbrekelmans/bdi
vendor/bin/bdi detect drivers
```

## Launch Scrapping

```bash
php src/startups-scraper.php
```

Data are exported to a csv file under `data/` directory

### TODO
- [x] Add support for other countries 

- [x] Scrape data without re-loading all pages

- [x] Refactoring

- [ ] Retry if failed

- [ ] Log errors in file
