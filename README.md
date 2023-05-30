# symfony_cda
# installer les composents du projet
composer install

# lancer le serveur
symfony server:start

# installer des composents sumfony
composer require

# commande de creation de base de donnée avec les controller du projet
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate