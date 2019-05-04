# Chapman Bar Exam

## Introduction


## Development

This project was developed with Larvel. [here](https://laravel.com/)

Just follow the instructions for installation and open the projects from the `SensorCapture.pro` file.


## Configuration

### Laradock

This project uses docker through lardock, cloned through a submodule in the root of the project

```
git submodule init 
git submodule update
cd laradock
cp env-example .env
```

#### Starting Docker Instance

```
docker-compose up -d nginx postgres adminer workspace 
```

#### workspace

```
docker-compose exec workspace bash
```

### Larvel

#### Configuration

environment configuration:
```
# for lardock
cp .env.lardock .env
# for larvel configuration
cp .env.example .env

# generate application key for salting
php artisan key:generate
``` 

#### database migration
```
php artisian migrate
```


installing packages: 
```
# install php packages
composer install 
# install node packages
yarn install
```

build static javascript

* `yarn run development` or `yarn run dev`
* `yarn run watch`
* `yarn run watch-poll`
* `yarn run hot`
* `yarn run production` or `yarn run prod`


# Libraries

## Frontend

- [Carbon Design System](https://www.carbondesignsystem.com/getting-started/developers/vanilla)
- [Bulma](https://bulma.io/)
- [Carbon Layout](https://www.carbondesignsystem.com/guidelines/layout)



## Maintainers

The current maintainers of this repository are:

* Michael Pollind <polli104@mail.chapman.edu>

# License

```
/**
* Copyright 2018 Chapman University
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

```