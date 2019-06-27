# Chapman Bar Exam

## Introduction


## Development

This project was developed with Symfony. [here](https://symfony.com/)

## Configuration

### Docker

This project uses docker through a docker-compose configuration in the root of the project

#### Starting Docker Instance

```
docker-compose up
docker-compose exec php bash
composer install
php ./bin/console doctrine:migrations:migrate
```

#### workspace

this allows the php instance to be accessed through bash. i.e running console commands for symfony and installing composer dependencies for PHP.
```
docker-compose exec php bash
```

### Symfony

#### Configuration

.env file in the root of the project file has enviroment variable configurations that are used by symfony. The .env file at at the root of the project is configured for use in a development enviroment.


#### database migration
```
php ./bin/console doctrine:migrations:generate 
```


installing packages: 
```
# install php packages
composer install 
# install node packages
yarn install
```

Note: when installing composer packages it's recommend to do this through the docker container since the system version of php might be diffrent then what is installed in the docker container. This can cause packages to break when viewing the site.


## Frontend
The frontend is built using [Symfony Encore](https://symfony.com/doc/current/frontend.html)

build static javascript. 

* `yarn run dev-server` -- live dev environment that hotloads elements in the the page when source changes
* `yarn run dev` -- dev build of the app (uncompressed with debug tooling)
* `yarn run watch` -- dev build but with resource reload
* `yarn run build` -- production build of the site


# API Platform
API platform is rest/graphql framework that is focused on defining an API. all the information about the routes and resources are defined through `App\Entity\*`.

## Notable Routes
- /_api/docs - information about resources and routes


# Libraries

## Frontend

- [Element UI](https://element.eleme.io/#/en-US/component/installation)
- [Bulma](https://bulma.io/)

## Backend
- [API Platform](https://api-platform.com/)
- [Doctrine](https://www.doctrine-project.org/)
- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle) - generate new auth tokens since the public and private token are exposed on this repository: [configuration](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration)
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
