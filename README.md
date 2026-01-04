docker compose up -d

docker exec -it cinema_management_php_php bash

cd yii-project/

php init

composer update

php yii migrate

php yii migrate --db=testDb


php yii user/create admin@gmail.com admin password123 ADMIN

php yii user/create user@gmail.com user password123 USER