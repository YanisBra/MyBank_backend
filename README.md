# MyBank_back

## Deploy localy
Install the dependances
```
composer install
```

Create the database and make the migrations
```
symfony console doctrine:database:create
symfony console doctrine:migration:migrate
```

Try locally
```
symfony serve
```

## Deploy with Docker

Create a network
```
docker network create symfony-mybank-network
```

If needed deploy a myslq container
```
docker run --name symfony-mybank-mysql --network symfony-mybank-network -p 3306:3306 -e MYSQL_ROOT_PASSWORD=root mysql
```

Change the connection string in the .env line 27 with the container name of mysql container

Build the image and deploy as container
```
docker build . -t MyBank_backend
docker run --name MyBank_backend_container --network symfony-mybank-network -p 8089:80 MyBank_backend
```

Create database in mysql container and make the migration
```
docker exec -it MyBank_backend_container php bin/console doctrine:database:create
docker exec -it MyBank_backend_container php bin/console doctrine:migration:migrate
```

## Deploy with Jenkins

If not already done start an instance of jenkins_master
```
docker run --name jenkins -p <choose_a_port>:8080 jenkins/jenkins
```

Then build and start an instance of a jenkins_agent
If your are on Windows, execute this command in Powershell or cmd
```
cd Jenkins-agent
docker build -t jenkins-agent-with-docker-and-composer .
docker run --init --name jenkins_agent_composer -v /var/run/docker.sock:/var/run/docker.sock jenkins-agent-with-docker-and-composer -url http://<Jenkins_master_IP_adress>:8080 <secret> <agent_name>
```