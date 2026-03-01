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

## Database Seeding

There are Postcode and Store seeders

> ./vendor/bin/sail artisan db:seed

## Tests

Tests can be run using 

> ./vendor/bin/sail artisan test

## Pint linting

To enforce PSR12 you can run Laravel Pint using

> ./vendor/bin/sail pint 

## Static Analysis

Run PHPstan

> ./vendor/bin/sail bin phpstan analyse

# Store API Documentation (v1)

This document outlines the available API endpoints for the Store Management and Spatial Search system. All endpoints are versioned under `v1` and return consistent JSON structures.

---

## Global Configuration

- **Base URL:** `{host}/api/v1`
- **Rate Limiting:** 60 requests per minute per IP.
- **Content-Type:** `application/json`

---

## Response Structures

### Success Response
```json
{
    "status": "success",
    "message": "Descriptive success message",
    "data": [] | {}
}

{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "field_name": ["Specific error message"]
    }
}

```

### Create Store

> URL: /api/v1/stores

Method: POST

Rate Limit: 60 requests / minute

#### Request Body (application/json)

| Field | Type | Required | Constraints | Description |
|---|---|---|---|---|
| name | string | Yes | max: 255 | Unique per address. |
| address | string | No | Max: 255 | Physical street address. |
| brand | string | No | Enum | StoreBrand (e.g tesco). |
| delivery_radius_km | numeric | Yes | 0.1 to 100 | Radius the store services |
| latitude | numeric | Yes  | -90 to 90 | GPS Latitude. |
| longitude | numeric | Yes | -180 to 180 | GPS Longitude. |

#### Success Response (210 Created)

```json

{
    "status": "success",
    "message": "Store created successfully.",
    "data": {
        "uuid": "9b8f2a1d-7c3e-4b5a-a123-456789abcdef",
        "name": "Central Coffee",
        "brand": {
            "id": "independent",
            "name": "Independent Store"
        },
        "address": "123 High St",
        "is_active": true,
        "location": {
            "lat": 51.5074,
            "lng": -0.1278
        },
        "created_at": "1 second ago",
        "delivery": {
            "radius_km": 5.0
        }
    }
}
```

#### Validation Error

```json

{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "name": [
            "A store with the same name and address already exists."
        ],
        "latitude": [
            "The latitude field is required."
        ]
    }
}

```

### Neaby Stores

> URL: /api/v1/nearbyA

Method: Get

Rate Limit: 60 requests / minute

#### Request Body (application/json)

| Field | Type | Required | Description |
|---|---|---|---|
| postcode | string | Yes | Valid uk postcode |
| radius | int | No | Between 0 and 100 |

#### Success Response (210 Created)

```json
{
  "data": [
    {
      "uuid": "8f2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d",
      "name": "Central Provisions",
      "brand": {
        "id": "tesco",
        "name": "Tesco"
      },
      "address": "123 High Street, London",
      "is_active": true,
      "location": {
        "lat": 51.5074,
        "lng": -0.1278
      },
      "created_at": "2 days ago",
      "delivery": {
        "radius_km": 5.5,
        "distance": "1.24 km",
        "estimated_delivery_minutes": 25
      }
    }
  ]
}
```

### Neaby Stores

> URL: /api/v1/can-deliver

Method: Get

Rate Limit: 60 requests / minute

#### Request Body (application/json)

| Field | Type | Required | Description |
|---|---|---|---|
| postcode | string | Yes | Valid uk postcode |
| radius | int | No | Between 0 and 100 |

#### Success Response (210 Created)

```json
{
  "data": [
    {
      "uuid": "8f2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d",
      "name": "Central Provisions",
      "brand": {
        "id": "local_harvest",
        "name": "Local Harvest Co."
      },
      "address": "123 High Street, London",
      "is_active": true,
      "location": {
        "lat": 51.5074,
        "lng": -0.1278
      },
      "created_at": "2 days ago",
      "delivery": {
        "radius_km": 5.5,
        "distance": "1.24 km",
        "estimated_delivery_minutes": 25
      }
    }
  ]
}