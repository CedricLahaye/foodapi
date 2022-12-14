<?php

namespace App\GraphQL\Resolver;

use App\Service\MutationService;
use App\Service\QueryService;
use ArrayObject;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\ArgumentInterface;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

class CustomResolverMap extends ResolverMap
{
    public function __construct(
        private QueryService    $queryService,
        private MutationService $mutationService
    ) {}

    /**
     * @inheritDoc
     */
    protected function map(): array
    {
        return [
            'RootQuery'    => [
                self::RESOLVE_FIELD => function (
                    $value,
                    ArgumentInterface $args,
                    ArrayObject $context,
                    ResolveInfo $info
                ) {
                    return match ($info->fieldName) {
                        'productById' => $this->queryService->findProductById((int)$args['id']),
                        'productByBarcode' => $this->queryService->findProductByBarcode($args['barcode']),
                        'productByName' => $this->queryService->findProductByName($args['name']),
                        default => null
                    };
                },
            ],
            'RootMutation' => [
                self::RESOLVE_FIELD => function (
                    $value,
                    ArgumentInterface $args,
                    ArrayObject $context,
                    ResolveInfo $info
                ) {
                    return match ($info->fieldName) {
                        'createProduct' => $this->mutationService->createProduct($args['product']),
                        'updateProductById' => $this->mutationService->updateProductById((int)$args['id'], $args['product']),
                        'updateProductByBarcode' => $this->mutationService->updateProductByBarcode($args['barcode'], $args['product']),
                        'deleteProductById' => $this->mutationService->deleteProductById((int)$args['id']),
                        'deleteProductByBarcode' => $this->mutationService->deleteProductByBarcode($args['barcode']),
                        default => null
                    };
                },
            ],
        ];
    }
}