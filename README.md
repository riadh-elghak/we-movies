# we-movies
technical test

## Requirements
* docker
* docker-compose

## Installation

1. run docker environment
```
docker-compose up -d
```

2. install php dependencies
```
docker-compose run composer install
```

3. install frontend dependencies
```
docker-compose run node npm i
docker-compose run node yarn install
docker-compose run node yarn build
```

Finaly go to this URL to see the app http://127.0.0.1:8000/
