# foodapi

Projet réalisé avec Symfony 6.1

Start server
````
symfony server:start
````
:warning: Don't forget to modify the .env file to match your database settings (example) :    
````
DATABASE_URL="mysql://username:password@127.0.0.1:8889/database_name?serverVersion=5.7"
`````
Create database 
`````
php bin/console doctrine:database:create
`````
Prepare the migration. 
`````
php bin/console make:migration
`````
 Make the migration. 
 ``````
 php bin/console doctrine:migrations:migrate
 
 ``````

:warning: The field "barcode" must be 13 characters long.  
:warning: The field "name" is optional to create and update, others are mandatory.  

# Routes de l'api : 

GET ALL (GET): /products/  
GET By ID (GET): /products/{id}  
CREATE (POST): /products/create  
EDIT (PUT): /products/update/{id}/.  
DELETE (POST):  /products/{id}  

UpdateStock (PUT) : /products/stock/{id}/?add=1 || /products/stock/{id}/?remove=1

------------


# GRAPHQL :

http://127.0.0.1:8000/graphiql To run from the server (interface)
http://127.0.0.1:8000/ To run from Postman

## GET


### Get by ID
````
query product {
  productByName(id: 1) {
    name,
    stocks,
    barcode
  }
}
````


### Get by Barcode
````
query product {
  productByBarcode(barcode: "0123456789123") {
    name,
    stocks,
    barcode
  }
}
````

### Get by name
````
query product {
  productByName(name: "Apple") {
    name,
    stocks,
    barcode
  }
}
````
## Create
````
mutation CreateProduct {
  createProduct(product: {name: "Pain de miou", stocks: 69, barcode: "3017620425035"}) {
    id,
    stocks,
    barcode,
    name
  }
}
````

## Update

### Update by ID : 
```
mutation UpdateProducts {
  updateProductById(id:11, product: {name: "Pain de mou", stocks: 32, barcode: "3017620425035"}) {
    id,
    stocks,
    barcode,
    name
  }
}
```

### Update by Barcode : 
```
mutation UpdateProducts {
  updateProductByBarcode(barcode: "3017620425035", product: {name: "Pain de mou", stocks: 32, barcode: "3017620425035"}) {
    id,
    stocks,
    barcode,
    name
  }
}
```
## Delete

### Delete by ID :
```
mutation DeleteProduct {
  deleteProductById(id:2) 
}
````
### Delete by Barcode : 
```
mutation DeleteProduct {
  deleteProductByBarcode(barcode:"3017620425035") 
}
````



