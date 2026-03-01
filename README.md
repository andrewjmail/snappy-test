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

The Api endpoints require Postcode data for the spatial calculations. There is a sample csv included in the repo which can be imported with the following steps.

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

> ./vendor/bin/sail bin/phpstan analyse

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

```

```json

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

#### Success Response (201 Created)

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

### Nearby Stores

> URL: /api/v1/nearby

Method: Get

Rate Limit: 60 requests / minute

#### Query Parameters (application/json)

| Field | Type | Required | Description |
|---|---|---|---|
| postcode | string | Yes | Valid uk postcode |
| radius | int | No | Between 0 and 100 |

#### Success Response (200 OK)

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

### Can Deliver

> URL: /api/v1/can-deliver

Method: Get

Rate Limit: 60 requests / minute

#### Query Parameters (application/json)

| Field | Type | Required | Description |
|---|---|---|---|
| postcode | string | Yes | Valid uk postcode |
| radius | int | No | Between 0 and 100 |

#### Success Response (200 OK)

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

```

### Database Schema

I haven't included any diagrams as it is very simple 

### 1. Stores Table
Stores the physical shop locations, branding, and their specific delivery capabilities.

| Field | Type | Nullable | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | NO | PRI | NULL | auto_increment |
| `uuid` | `varchar(255)` | NO | UNI | NULL | |
| `name` | `varchar(50)` | NO | | NULL | |
| `address` | `varchar(255)` | YES | | NULL | |
| `brand` | `varchar(50)` | YES | MUL | NULL | Indexed Enum |
| `delivery_radius_km` | `decimal(5,2)` | NO | | `5.00` | |
| `location` | `point` | NO | MUL | NULL | **Spatial Index** |
| `active_at` | `datetime` | YES | | NULL | |
| `created_at` | `timestamp` | YES | | NULL | |
| `updated_at` | `timestamp` | YES | | NULL | |

### 2. Postcodes Table
A spatial lookup table used to translate UK postcodes into coordinates for radius searches.

| Field | Type | Nullable | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `bigint unsigned` | NO | PRI | NULL | auto_increment |
| `postcode` | `varchar(10)` | NO | UNI | NULL | Unique Index |
| `location` | `point` | NO | MUL | NULL | **Spatial Index** |
| `created_at` | `timestamp` | YES | | NULL | |
| `updated_at` | `timestamp` | YES | | NULL | |

### Architecture 

The architecture is pretty standard for a laravel API. I used a service class to group the store logic into a class for reuse and testability, created a Resource class to customise the response the API returned and a trait to give a consistent response structure.

The postcode importing command uses

- LazyCollection to limit the memory usage incase of large import files.
- Chunking to further limit the memory usage
- Queued batches to allow for multiple processes to process the import
- The job_batch record to allow tracking of individual jobs 
- Service class to make that logic testable

The job uses bulk upserts to efficiently update / create the data.
