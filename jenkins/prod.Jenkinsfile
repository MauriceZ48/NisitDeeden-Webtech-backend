pipeline {
    agent any
    environment {
        DOCKER = "/usr/local/bin/docker"
        SAIL = "./vendor/bin/sail"
    }

    stages {
        stage('Setup ENV') {
            steps {
                withVault([
                    vaultSecrets: [
                        [
                            path: 'kv/nisit-deeden-backend-production',
                            secretValues: [
                                [envVar: 'APP_NAME', vaultKey: 'APP_NAME'],
                                [envVar: 'APP_ENV', vaultKey: 'APP_ENV'],
                                [envVar: 'APP_KEY', vaultKey: 'APP_KEY'],
                                [envVar: 'APP_DEBUG', vaultKey: 'APP_DEBUG'],
                                [envVar: 'APP_URL', vaultKey: 'APP_URL'],
                                
                                [envVar: 'APP_LOCALE', vaultKey: 'APP_LOCALE'],
                                [envVar: 'APP_FALLBACK_LOCALE', vaultKey: 'APP_FALLBACK_LOCALE'],
                                [envVar: 'APP_FAKER_LOCALE', vaultKey: 'APP_FAKER_LOCALE'],
                                [envVar: 'APP_MAINTENANCE_DRIVER', vaultKey: 'APP_MAINTENANCE_DRIVER'],

                                [envVar: 'PHP_CLI_SERVER_WORKERS', vaultKey: 'PHP_CLI_SERVER_WORKERS'],
                                [envVar: 'BCRYPT_ROUNDS', vaultKey: 'BCRYPT_ROUNDS'],

                                [envVar: 'LOG_CHANNEL', vaultKey: 'LOG_CHANNEL'],
                                [envVar: 'LOG_STACK', vaultKey: 'LOG_STACK'],
                                [envVar: 'LOG_DEPRECATIONS_CHANNEL', vaultKey: 'LOG_DEPRECATIONS_CHANNEL'],
                                [envVar: 'LOG_LEVEL', vaultKey: 'LOG_LEVEL'],
                                
                                [envVar: 'DB_CONNECTION', vaultKey: 'DB_CONNECTION'],
                                [envVar: 'DB_HOST', vaultKey: 'DB_HOST'],
                                [envVar: 'DB_PORT', vaultKey: 'DB_PORT'],
                                [envVar: 'DB_DATABASE', vaultKey: 'DB_DATABASE'],
                                [envVar: 'DB_USERNAME', vaultKey: 'DB_USERNAME'],
                                [envVar: 'DB_PASSWORD', vaultKey: 'DB_PASSWORD'],

                                [envVar: 'SESSION_DRIVER', vaultKey: 'SESSION_DRIVER'],
                                [envVar: 'SESSION_LIFETIME', vaultKey: 'SESSION_LIFETIME'],
                                [envVar: 'SESSION_ENCRYPT', vaultKey: 'SESSION_ENCRYPT'],
                                [envVar: 'SESSION_PATH', vaultKey: 'SESSION_PATH'],
                                [envVar: 'SESSION_DOMAIN', vaultKey: 'SESSION_DOMAIN'],

                                [envVar: 'BROADCAST_CONNECTION', vaultKey: 'BROADCAST_CONNECTION'],
                                [envVar: 'FILESYSTEM_DISK', vaultKey: 'FILESYSTEM_DISK'],
                                [envVar: 'QUEUE_CONNECTION', vaultKey: 'QUEUE_CONNECTION'],
                                [envVar: 'CACHE_STORE', vaultKey: 'CACHE_STORE'],
                                [envVar: 'MEMCACHED_HOST', vaultKey: 'MEMCACHED_HOST'],
                                [envVar: 'VITE_APP_NAME', vaultKey: 'VITE_APP_NAME'],
                            ]
                        ]
                    ]
                ]) {
                    sh '''printenv | grep -E '^(APP_|DB_|SESSION_|CACHE_|QUEUE_|BROADCAST_|FILESYSTEM_|MEMCACHED_|VITE_|BCRYPT_ROUNDS|PHP_CLI_SERVER_WORKERS)' | sort > .env'''
                }
            }
        }
        
        stage('Setup Laravel & Deploy') {
            steps {
                sh '''
                ${DOCKER} run --rm \
                  -u "$(id -u):$(id -g)" \
                  -v "$(pwd):/app" \
                  -w /app \
                  composer:latest \
                  install --ignore-platform-reqs
                '''
                sh '${SAIL} down -v'
                sh '${SAIL} up -d'
                sh '''
                    echo "Waiting for MySQL..."
                    until ./vendor/bin/sail exec -T mysql sh -c 'mysql -u$MYSQL_USER -p $MYSQL_PASSWORD -e "SELECT 1"' ; do
                        sleep 3
                    done
                    echo "MySQL ready!"
                '''
                sh '${SAIL} artisan migrate:fresh --seed'
                sh '${SAIL} yarn install'
                sh '${SAIL} yarn build'
            }
        }
    }
}
