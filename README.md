# MediuswareTest

## Installation using docker

>Make sure you have `docker` and `docker-compose` installed in your machine

Set the following local dns entries in your /etc/hosts file

``` 
127.0.0.1 www.mediusware.test
```

>The default password for MySQL is `password` and the username is `root`

## Update your .env

>`database` will be the host address for the database

## Run application
Set executable permission to the `start` file, which is located at the root directory of the project

Run the below command to run the application, it will take few minutes for the first time
```
./start
```

## Browse app
```
http://www.mediusware.test
```

## List running docker container
```
docker ps
```

## Execute artisan command
```
docker exec -it mediusware-app php artisan migrate
```

## Execute composer command
```
docker exec -it mediusware-app composer install
```

## Run test
```
docker exec -it mediusware-app php artisan test
```

## Access database client
```
url: http://www.mediusware.test:1010/
server: database
username: root
password: password
```