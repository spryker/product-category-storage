<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\CategoryTransfer;
use Generated\Shared\Transfer\NodeCollectionTransfer;
use Generated\Shared\Transfer\NodeTransfer;
use Generated\Shared\Transfer\ProductCategoryTransfer;
use Orm\Zed\Category\Persistence\SpyCategory;
use Orm\Zed\Category\Persistence\SpyCategoryNode;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategory;
use Propel\Runtime\Collection\Collection;

class ProductCategoryMapper
{
    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\ProductCategory\Persistence\SpyProductCategory> $productCategoryEntities
     * @param array<\Generated\Shared\Transfer\ProductCategoryTransfer> $productCategoryTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryTransfer>
     */
    public function mapProductCategoryEntitiesToProductCategoryTransfers(
        Collection $productCategoryEntities,
        array $productCategoryTransfers
    ): array {
        foreach ($productCategoryEntities as $productCategoryEntity) {
            $productCategoryTransfer = $this->mapProductCategoryEntityToProductCategoryTransfer(
                $productCategoryEntity,
                new ProductCategoryTransfer(),
            );

            $categoryTransfer = $this->mapCategoryEntityToCategoryTransfer(
                $productCategoryEntity->getSpyCategory(),
                new CategoryTransfer(),
            );

            $productCategoryTransfer->setCategory($categoryTransfer);
            $productCategoryTransfers[] = $productCategoryTransfer;
        }

        return $productCategoryTransfers;
    }

    protected function mapProductCategoryEntityToProductCategoryTransfer(
        SpyProductCategory $productCategoryEntity,
        ProductCategoryTransfer $productCategoryTransfer
    ): ProductCategoryTransfer {
        return $productCategoryTransfer->fromArray($productCategoryEntity->toArray(), true);
    }

    protected function mapCategoryEntityToCategoryTransfer(
        SpyCategory $productCategoryEntity,
        CategoryTransfer $categoryTransfer
    ): CategoryTransfer {
        $categoryTransfer
            ->fromArray($productCategoryEntity->toArray(), true)
            ->setNodeCollection(new NodeCollectionTransfer());

        foreach ($productCategoryEntity->getNodes() as $categoryNodeEntity) {
            $categoryTransfer->getNodeCollectionOrFail()
                ->addNode($this->mapCategoryNodeEntityToNodeTransfer($categoryNodeEntity, new NodeTransfer()));
        }

        return $categoryTransfer;
    }

    protected function mapCategoryNodeEntityToNodeTransfer(
        SpyCategoryNode $categoryNodeEntity,
        NodeTransfer $nodeTransfer
    ): NodeTransfer {
        return $nodeTransfer->fromArray($categoryNodeEntity->toArray(), true);
    }
}
