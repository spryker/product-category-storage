<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage\Reader;

use Generated\Shared\Transfer\CategoryPathNodeCollectionTransfer;
use Generated\Shared\Transfer\CategoryPathNodeTransfer;
use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Spryker\Client\ProductCategoryStorage\Storage\ProductAbstractCategoryStorageReaderInterface;
use Spryker\Service\Category\CategoryServiceInterface;

class ProductAbstractCategoryPathReader implements ProductAbstractCategoryPathReaderInterface
{
    /**
     * The category paths read in this request, by store, locale and abstract product ID; an empty list for a product without one.
     * Static, as a page renders several product lists, e.g. the product and its relations, whose products overlap; a storefront
     * request is one process, so the cache never outlives the page.
     *
     * @var array<string, array<string, array<int, list<string>>>>
     */
    protected static array $categoryPathsCache = [];

    public function __construct(
        protected ProductAbstractCategoryStorageReaderInterface $productAbstractCategoryStorageReader,
        protected CategoryServiceInterface $categoryService
    ) {
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<int, list<string>>
     */
    public function getProductAbstractCategoryPaths(array $productAbstractIds, string $localeName, string $storeName): array
    {
        $uniqueProductAbstractIds = array_values(array_unique(array_filter($productAbstractIds)));

        if (!$uniqueProductAbstractIds) {
            return [];
        }

        $this->readNotCachedCategoryPaths($uniqueProductAbstractIds, $localeName, $storeName);

        $categoryPathsIndexedByIdProductAbstract = [];

        foreach ($uniqueProductAbstractIds as $idProductAbstract) {
            $categoryPath = static::$categoryPathsCache[$storeName][$localeName][$idProductAbstract] ?? [];

            if (!$categoryPath) {
                continue;
            }

            $categoryPathsIndexedByIdProductAbstract[$idProductAbstract] = $categoryPath;
        }

        return $categoryPathsIndexedByIdProductAbstract;
    }

    /**
     * @param list<int> $productAbstractIds
     */
    protected function readNotCachedCategoryPaths(array $productAbstractIds, string $localeName, string $storeName): void
    {
        $notCachedProductAbstractIds = [];

        foreach ($productAbstractIds as $idProductAbstract) {
            if (isset(static::$categoryPathsCache[$storeName][$localeName][$idProductAbstract])) {
                continue;
            }

            $notCachedProductAbstractIds[] = $idProductAbstract;
            static::$categoryPathsCache[$storeName][$localeName][$idProductAbstract] = [];
        }

        if (!$notCachedProductAbstractIds) {
            return;
        }

        $productAbstractCategoryStorageTransfers = $this->productAbstractCategoryStorageReader
            ->findBulkProductAbstractCategory($notCachedProductAbstractIds, $localeName, $storeName);

        foreach ($productAbstractCategoryStorageTransfers as $productAbstractCategoryStorageTransfer) {
            static::$categoryPathsCache[$storeName][$localeName][$productAbstractCategoryStorageTransfer->getIdProductAbstractOrFail()]
                = $this->categoryService->getCategoryPath(
                    $this->mapProductAbstractCategoryStorageTransferToCategoryPathNodeCollectionTransfer(
                        $productAbstractCategoryStorageTransfer,
                        new CategoryPathNodeCollectionTransfer(),
                    ),
                );
        }
    }

    protected function mapProductAbstractCategoryStorageTransferToCategoryPathNodeCollectionTransfer(
        ProductAbstractCategoryStorageTransfer $productAbstractCategoryStorageTransfer,
        CategoryPathNodeCollectionTransfer $categoryPathNodeCollectionTransfer
    ): CategoryPathNodeCollectionTransfer {
        foreach ($productAbstractCategoryStorageTransfer->getCategories() as $productCategoryStorageTransfer) {
            $categoryPathNodeCollectionTransfer->addNode(
                (new CategoryPathNodeTransfer())
                    ->setIdCategory($productCategoryStorageTransfer->getCategoryIdOrFail())
                    ->setName($productCategoryStorageTransfer->getName())
                    ->setParentCategoryIds($productCategoryStorageTransfer->getParentCategoryIds()),
            );
        }

        return $categoryPathNodeCollectionTransfer;
    }
}
