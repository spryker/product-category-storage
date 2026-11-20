<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage\Expander;

use Spryker\Client\ProductCategoryStorage\Reader\ProductAbstractCategoryPathReaderInterface;

class CategoryPathExpander implements CategoryPathExpanderInterface
{
    public function __construct(protected ProductAbstractCategoryPathReaderInterface $productAbstractCategoryPathReader)
    {
    }

    /**
     * @param array<\Generated\Shared\Transfer\ProductViewTransfer> $productViewTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function expandProductViewTransfersWithCategoryPath(array $productViewTransfers, string $localeName, string $storeName): array
    {
        $productAbstractIds = [];

        foreach ($productViewTransfers as $productViewTransfer) {
            $idProductAbstract = $productViewTransfer->getIdProductAbstract();

            if (!$idProductAbstract) {
                continue;
            }

            $productAbstractIds[] = $idProductAbstract;
        }

        $categoryPaths = $this->productAbstractCategoryPathReader->getProductAbstractCategoryPaths($productAbstractIds, $localeName, $storeName);

        foreach ($productViewTransfers as $productViewTransfer) {
            $productViewTransfer->setCategoryPath($categoryPaths[$productViewTransfer->getIdProductAbstract()] ?? []);
        }

        return $productViewTransfers;
    }
}
