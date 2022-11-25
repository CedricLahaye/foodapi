# foodapi

Projet réalisé avec Symfony 6.1

symfony server:start : Lancement du serveur

php bin/console doctrine:database:create : Créer la base de données  
php bin/console make:migration : Préparer la migration de la base  
php bin/console doctrine:migrations:migrate : Effectuer la migration 


# Routes de l'api : 

GET ALL (GET): /products/  
GET By ID (GET): /products/{id}  
CREATE (POST): /products/create  
EDIT (PUT): /products/update/{id}/.  
DELETE (POST):  /products/{id}  

UpdateStock (PUT) : /products/stock/{id}/?add=1 || /products/stock/{id}/?remove=1
