<?php

namespace App\Service;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\Collection;
use GraphQL\Error\Error;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class QueryService
{
    public function __construct(
        private ProductsRepository $productsRepository,
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function findProductById(int $productId): ?Products
    {

        $product = $this->productsRepository->find($productId);
        if (is_null($product)) {
            throw new Error("Could not find product for specified Id");
        }
        return $this->GetNameFromApi($product);
    }
    public function findProductByBarcode(string $productBarCode): ?Products{
        $product = $this->productsRepository->findOneBy(['barcode' => $productBarCode]);
        if (is_null($product)) {
            throw new Error("Could not find product for specified Barcode");
        }
        return $this->GetNameFromApi($product);
    }
    public function findProductByName(string $name): ?Products{
        $product =  $this->productsRepository->findOneBy(['name' => $name]);
        if (is_null($product)) {
            throw new Error("Could not find product for specified Name");
        }
        return $this->GetNameFromApi($product);
    }

    /**
     * @param Products|null $product
     * @return Products|null
     * @throws TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    public function GetNameFromApi(?Products $product): ?Products
    {
        if ($product->getName() == null) {
            $barcode = $product->getBarcode();
            $url = "https://world.openfoodfacts.org/api/v2/search?code=',$barcode,'&fields=code,product_name";
            $client = HttpClient::create();
            $response = $client->request('GET', $url);
            $statusCode = $response->getStatusCode();
            $contentType = $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
            $content = $response->toArray();
            $name = $content['products'][0]['product_name'];
            $barcode = $content['products'][0]['code'];
            $product->setName($name);
            $product->setBarcode($barcode);
        }
        return $product;
    }
}