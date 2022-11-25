<?php

namespace App\Controller;

use App\Entity\Products;
use App\Form\ProductsType;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function PHPUnit\Framework\returnArgument;

#[Route('/products')]
class ProductsController extends AbstractController
{
    /*
    //Get All products
    #[Route('/', name: 'app_products_index', methods: ['GET'])]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('products/index.html.twig', [
            'products' => $productsRepository->findAll(),
        ]);
    }

    //Create a Product
    #[Route('/create', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductsRepository $productsRepository): Response
    {
        $product = new Products();
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsRepository->save($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }
    //Get Product by ID
    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    public function show(Products $product): Response
    {
        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }

    //Edit a Product By ID
    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {
        $form = $this->createForm(ProductsType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productsRepository->save($product, true);

            return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('products/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    //Delete a Product By ID
    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function delete(Request $request, Products $product, ProductsRepository $productsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productsRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_products_index', [], Response::HTTP_SEE_OTHER);
    }
    */

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param ProductsRepository $productsRepository
     * @Route("/", name="api_products", methods={"GET"})
     * @return Response
     */
    public function getAllProducts(ProductsRepository  $productsRepository): Response
    {
        return $this->json($productsRepository->findAll(), Response::HTTP_OK);
    }

    #[Route('/create', name: 'app_products_new', methods: ['GET', 'POST'])]
    public function createProduct(ValidatorInterface $validator, Request $request): Response
    {
        $product = new Products();
        $name = $request->get('name');
        $stock = $request->get('stocks');
        if(empty($name)) return $this->json("Enter a name");
        if(empty($stock)) return $this->json("Enter amount of stocks");
        $product->setName($name);
        $product->setStocks($stock);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors[0], Response::HTTP_BAD_REQUEST);
        }

        $this->em->persist($product);
        $this->em->flush();

        return $this->json('Created new product successfully', Response::HTTP_CREATED );
    }

    #[Route('/{id}', name: 'app_products_show', methods: ['GET'])]
    public function getSelectedProduct(int $id, ProductsRepository $productsRepository): Response
    {
        $res = $productsRepository->find($id);
        if($res != null){
            return $this->json($res, Response::HTTP_OK);
        }
        return $this->json("This product does not exists", Response::HTTP_NOT_FOUND);
    }

    #[Route('/{id}/edit', name: 'app_products_edit', methods: ['PUT'])]
    public function modifyProduct(Request $request, int $id, ValidatorInterface $validator): Response
    {
        $product = $this->em->getRepository(Products::class)->find($id);

        if(!$product){
            return $this->json('No product found for id '. $id, 404);
        }
        if(!$product instanceof Products){
            throw new \LogicException('Old product not found');
        }

        $product->setName($request->get('name'));
        $product->setStocks($request->get('stocks'));

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors[0], Response::HTTP_BAD_REQUEST);
        }

        $this->em->flush();

        $newData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'stocks' => $product->getStocks()
        ];
        return $this->json($newData, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_products_delete', methods: ['POST'])]
    public function deleteProduct(int $id): Response
    {
        $product = $this->em->getRepository(Products::class)->find($id);

        if(!$product){
            return $this->json('No product found for id '. $id, Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($product);
        $this->em->flush();

        return $this->json('Deleted product '. $id . ' successfully', Response::HTTP_OK);
    }

    #[Route('/{id}/stock', name: 'app_products_changestock', methods: ['PUT'])]
    public function changeStock(Request $request, int $id, ValidatorInterface $validator): Response
    {
        $product = $this->em->getRepository(Products::class)->find($id);

        if(!$product){
            return $this->json('No product found for id '. $id, 404);
        }
        if(!$product instanceof Products){
            throw new \LogicException('Old product not found');
        }

        if($stock = $request->get("add")){
            if(!is_numeric($stock)) return $this->json("Please enter a number");
            $product->setStocks($product->getStocks()+$stock);
        }else if($stock = $request->get("remove")){
            if(!is_numeric($stock)) return $this->json("Please enter a number");
            $product->setStocks($product->getStocks()-$stock);
        }else{
            return $this->json("Specify add or remove to change the stock");
        }


        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors[0], Response::HTTP_BAD_REQUEST);
        }

        $this->em->flush();

        $newData = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'stocks' => $product->getStocks()
        ];
        return $this->json($newData, Response::HTTP_OK);
    }
}
