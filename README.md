## Project Setup

Install the composer packages using Docker if Composer isn't installed locally

> docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

and run the setup script from the composer.json file to complete setup

> ./vendor/bin/sail composer setup

There is a devcontainer.json setup for local development. You can read about devcontainers [here](https://code.visualstudio.com/docs/devcontainers/containers).

Inside the devcontainer you will be able to use php artisan, composer, npm etc without the need for the sail alias.

## Postcode Importing Command

The Api endpoints require Postcode data for the spatial calcualations. There is a sample csv included in the repo which can be imported with the following steps.

> ./vendor/bin/sail artisan import:postcodes data/postcodes_sample.csv

If you would like to import a csv with different heading names you can override the default by passing the following options to the command

> ./vendor/bin/sail artisan import:postcodes path/to/your_file.csv \
    --postcode-col="YourPostcodeHeader" \
    --lat-col="YourLatitudeHeader" \
    --lon-col="YourLongitudeHeader"

Once the command has completed you can process the jobs using

> ./vendor/bin/sail artisan queue:work

## Tests

Tests can be run using 

> ./vendor/bin/sail artisan test

## Pint linting

To enforce PSR12 you can run Laravel Pint using

> ./vendor/bin/sail pint

## Pint linting

To enforce PSR12 you can run Laravel Pint using

> ./vendor/bin/sail pint

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
