<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage;

use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;

interface ProductCategoryStorageClientInterface
{
    /**
     * Specification:
     * - Returns Product Abstract Category by id for given store and locale.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Client\ProductCategoryStorage\ProductCategoryStorageClientInterface::findBulkProductAbstractCategory()} instead.
     *
     * @param int $idProductAbstract
     * @param string $localeName
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer|null
     */
    public function findProductAbstractCategory(
        int $idProductAbstract,
        string $localeName,
        string $storeName
    ): ?ProductAbstractCategoryStorageTransfer;

    /**
     * Specification:
     * - Returns Product Abstract Categories grouped by Product Abstract ids for given store and locale.
     * - Executes {@link \Spryker\Client\ProductCategoryStorageExtension\Dependency\Plugin\ProductAbstractCategoryStorageCollectionExpanderPluginInterface} plugins stack.
     *
     * @api
     *
     * @param array<int> $productAbstractIds
     * @param string $localeName
     * @param string $storeName
     *
     * @return array<\Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer>
     */
    public function findBulkProductAbstractCategory(array $productAbstractIds, string $localeName, string $storeName): array;

    /**
     * Specification:
     * - Reads the published categories of the abstract products of the given product views for the given store and locale with one storage request.
     * - Executes {@link \Spryker\Client\ProductCategoryStorageExtension\Dependency\Plugin\ProductAbstractCategoryStorageCollectionExpanderPluginInterface} plugins stack.
     * - Resolves the category path of each abstract product with {@link \Spryker\Service\Category\CategoryServiceInterface::getCategoryPath()} from `ProductCategoryStorage.parentCategoryIds`:
     *   the deepest published category with the categories above it; among equally deep categories, the one with the lowest category id.
     * - Sets `ProductViewTransfer.categoryPath` of every product view: the category names, top level first, without the root category.
     * - Sets an empty `ProductViewTransfer.categoryPath` for a product view without `idProductAbstract` or without published categories.
     * - Keeps the keys of the given array.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductViewTransfer> $productViewTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductViewTransfer>
     */
    public function expandProductViewTransfersWithCategoryPath(array $productViewTransfers, string $localeName, string $storeName): array;

    /**
     * Specification:
     * - Requires `ProductCategoryStorageTransfer.url` to be set.
     * - Returns Product Categories filtered by http referer.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     * @param string $httpReferer
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>
     */
    public function filterProductCategoriesByHttpReferer(array $productCategoryStorageTransfers, string $httpReferer): array;

    /**
     * Specification:
     * - Requires `ProductCategoryStorageTransfer.categoryId` to be set.
     * - Returns Product Categories sorted in order from parent to child.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>
     */
    public function sortProductCategories(array $productCategoryStorageTransfers): array;
}
