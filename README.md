# foodapi

Projet réalisé avec Symfony 6.1

symfony server:start : Lancement du serveur

php bin/console doctrine:database:create : Créer la base de données  
php bin/console make:migration : Préparer la migration de la base  
php bin/console doctrine:migrations:migrate : Effectuer la migration 


# Route de l'api : 

GET ALL (GET): /products/  
GET By ID (GET): /products/{id}  
CREATE (POST): /products/create  
EDIT (PUT): /products/{id}/edit.  
DELETE (POST):  /products/{id}  

UpdateStock (PUT) : /products/{id}/stock?add=1 || /products/{id}/stock?remove=1
