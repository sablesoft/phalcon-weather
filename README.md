Phalcon OpenWeather - Test Work
=====

Phalcon service for the [Open Weather API](http://openweathermap.org/current).
Test work.

## Requirements
- Git
- Docker && Docker Compose

## Installation

```shell script
git clone https://github.com/sablesoft/phalcon-weather.git
```

## Configuration
1. Setup Dotenv
2. Setup Docker Compose
3. Start Docker Compose

##### Setup Dotenv:
```shell script
cp .env.dist .env
vim .env
```
- Set db credentials
- Set your open-weather api key

##### Setup Docker Compose:
```shell script
vim docker-compose.yml
```
- Share `db` and `redis` ports if needed
- Setup `docker/php/xdebug.ini` and inject if needed 

##### Start Docker Compose:
```shell script
docker-compose up -d
```
- Run command
- Waiting for compose ready

## Usage

Just run `/` url on your web-server host, and your get full help about this demo usage.

---
**NOTE**

It is recommended to use Google Chrome with JSONView extension installed

---