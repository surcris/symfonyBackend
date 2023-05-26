# symfony_cda

# installer des composent sumfony
composer require

# commande de creation de base de donnée avec les controller du projet
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate