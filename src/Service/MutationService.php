<?php

namespace App\Service;

use App\Entity\Products;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Error\Error;

class MutationService
{
    public function __construct(
        private EntityManagerInterface $manager
    ) {}

    public function createProduct(array $productDetails): Products
    {
        $product = new Products();
        if(isset($productDetails['name'])){
            $product->setName($productDetails['name']);
        }

        $product->setStocks($productDetails['stocks']);
        $product->setBarcode($productDetails['barcode']);

        if(strlen($productDetails['barcode']) != 13) throw new Error("Barcode must be 13 characters long");

        $CheckExisting = $this->manager->getRepository(Products::class)->findOneBy(['barcode' => $product->getBarcode()]);
        if($CheckExisting != null) throw new Error("This product already exists");

        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }

    public function updateProductById(int $id, array $productDetails): Products
    {
        $product = $this->manager->getRepository(Products::class)->find($id);

        if (is_null($product)) {
            throw new Error("Could not find product for specified ID");
        }

        if(isset($productDetails['name'])){
            $product->setName($productDetails['name']);
        }


        $product->setStocks($productDetails['stocks']);
        $product->setBarcode($productDetails['barcode']);

        if(strlen($productDetails['barcode']) != 13) throw new Error("Barcode must be 13 characters long");


        $CheckExisting = $this->manager->getRepository(Products::class)->findBy(['barcode' => $product->getBarcode()]);
        if(count($CheckExisting) > 1) throw new Error("This product already exists");

        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }
    public function updateProductByBarcode(string $barcode, array $productDetails): Products
    {
        $product = $this->manager->getRepository(Products::class)->findOneBy(['barcode' => $barcode]);

        if (is_null($product)) {
            throw new Error("Could not find product for specified Barcode");
        }

        if(isset($productDetails['name'])){
            $product->setName($productDetails['name']);
        }

        $product->setStocks($productDetails['stocks']);
        $product->setBarcode($productDetails['barcode']);

        if(strlen($productDetails['barcode']) != 13) throw new Error("Barcode must be 13 characters long");

        $CheckExisting = $this->manager->getRepository(Products::class)->findBy(['barcode' => $product->getBarcode()]);
        if(count($CheckExisting) > 1) throw new Error("This product already exists");

        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }

    public function deleteProductById(int $id): string{
        $product = $this->manager->getRepository(Products::class)->find($id);

        if(!$product){
            throw new Error("Could not find product for specified Id");
        }

        $this->manager->remove($product);
        $this->manager->flush();

        return "Product with ID ".$id." successfully deleted";
    }
    public function deleteProductByBarcode(string $barcode): string{
        $product = $this->manager->getRepository(Products::class)->findOneBy(['barcode' => $barcode]);

        if(!$product){
            throw new Error("Could not find product for specified Id");
        }

        $this->manager->remove($product);
        $this->manager->flush();

        return "Product with Barcode ".$barcode." successfully deleted";
    }
}