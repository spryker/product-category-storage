<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Persistence\Propel\Mapper;

use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Propel\Runtime\Collection\ObjectCollection;

class ProductCategoryStorageMapper
{
    /**
     * @param \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\ProductCategoryStorage\Persistence\SpyProductAbstractCategoryStorage> $productAbstractCategoryStorageEntities
     * @param array<array<array<\Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer>>> $productAbstractCategoryStorageTransfers
     *
     * @return array<array<array<\Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer>>>
     */
    public function mapProductAbstractCategoryStorageEntitiesToProductAbstractCategoryStorageTransfers(
        ObjectCollection $productAbstractCategoryStorageEntities,
        array $productAbstractCategoryStorageTransfers
    ): array {
        foreach ($productAbstractCategoryStorageEntities as $productAbstractCategoryStorageEntity) {
            $idProductAbstract = $productAbstractCategoryStorageEntity->getFkProductAbstract();
            $locale = $productAbstractCategoryStorageEntity->getLocale();
            $store = $productAbstractCategoryStorageEntity->getStore();

            $productAbstractCategoryStorageTransfers[$idProductAbstract][$store][$locale] = (new ProductAbstractCategoryStorageTransfer())
                ->fromArray($productAbstractCategoryStorageEntity->getData(), true);
        }

        return $productAbstractCategoryStorageTransfers;
    }
}
