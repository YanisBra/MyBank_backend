pipeline {
    agent {
        label "${AGENT}"
    }

    stages {
        
        stage('Continuous Integration') {
            steps {
                git branch: 'main', url: 'https://github.com/YanisBra/MyBank_backend.git'
                sh 'composer install --no-scripts'
                sh 'php bin/console assets:install public'
                sh 'php bin/console importmap:install'
            }
        }
        
        stage('Continuous Delivery') {
            steps {
                sh "docker build . -t ${DOCKERHUB_USERNAME}/mybank_backend"
                sh "docker login -u ${DOCKERHUB_USERNAME} -p ${DOCKER_PASSWORD}" 
                sh "docker push ${DOCKERHUB_USERNAME}/mybank_backend"
            }
        }

        stage('Continuous Deployment') {
            steps {
                sh '''
                    sshpass -p ${SERVER_PSW} ssh -o StrictHostKeyChecking=no ${SERVER_USER}@${SERVER_IP} \
                    "curl -O https://raw.githubusercontent.com/YanisBra/MyBank_backend/refs/heads/main/compose.yaml &&\
                    docker compose down || true &&\
                    docker compose up -d &&\
                    sleep 10 &&\
                    docker exec mybank_backend_container php bin/console doctrine:migrations:migrate --no-interaction &&\
                    docker exec mybank_backend_container php bin/console doctrine:fixtures:load --no-interaction &&\
                    docker exec mybank_backend_container php bin/console lexik:jwt:generate-keypair &&\
                    docker exec mybank_backend_container php bin/console cache:clear"
                '''
            }
        }

    }
}




// --------- Use this Jenkinsfile only if Jenkins runs on the deployment server ---------
/*pipeline {
    agent {
        label "${AGENT}"
    }

    stages {
        stage('Continuous Integration') {
            steps {
                git branch: 'main', url: 'https://github.com/YanisBra/MyBank_backend.git'
                sh 'composer install --no-scripts'
                sh 'php bin/console assets:install public'
                sh 'php bin/console importmap:install'
            }
        }
        
        stage('Continuous Delivery') {
            steps {
                sh "docker build . -t ${DOCKERHUB_USERNAME}/mybank_backend"
                sh "docker login -u ${DOCKERHUB_USERNAME} -p ${DOCKER_PASSWORD}" 
                sh "docker push ${DOCKERHUB_USERNAME}/mybank_backend"
            }
        }


        stage('Stop & Remove previous containers') {
            steps {
                sh 'docker compose down || true'
            }
        }

        stage('Continuous Deployment') {
            steps {
                sh 'docker compose up -d'
            }
        }

        stage('Symfony Setup') {
            steps {
                sh 'sleep 10'
                sh 'docker exec mybank_backend_container php bin/console doctrine:migrations:migrate --no-interaction'
                sh 'docker exec mybank_backend_container php bin/console doctrine:fixtures:load --no-interaction'
                sh 'docker exec mybank_backend_container php bin/console lexik:jwt:generate-keypair'
                sh 'docker exec mybank_backend_container php bin/console cache:clear'   
            }
        }
    }
} */

