<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage;

interface ProductCategoryStorageClientInterface
{
    /**
     * Specification:
     * - Returns Product Abstract Category by id.
     * - Forward compatibility (from next major): only product abstract categories assigned with passed $storeName will be returned.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Client\ProductCategoryStorage\ProductCategoryStorageClientInterface::findBulkProductAbstractCategory()} instead.
     *
     * @param int $idProductAbstract
     * @param string $locale
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer|null
     */
    public function findProductAbstractCategory($idProductAbstract, $locale, ?string $storeName = null);

    /**
     * Specification:
     * - Returns Categories grouped by Product Abstract id.
     * - Executes {@link \Spryker\Client\ProductCategoryStorageExtension\Dependency\Plugin\ProductAbstractCategoryStorageCollectionExpanderPluginInterface} plugins stack.
     * - Forward compatibility (from next major): only product abstract categories assigned with passed $storeName will be returned.
     *
     * @api
     *
     * @param int[] $productAbstractIds
     * @param string $localeName
     * @param string|null $storeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer[]
     */
    public function findBulkProductAbstractCategory(array $productAbstractIds, string $localeName, ?string $storeName = null): array;

    /**
     * Specification:
     * - Requires `ProductCategoryStorageTransfer.url` to be set.
     * - Returns Product Categories filtered by http referer.
     *
     * @api
     *
     * @param array<int, \Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     * @param string $httpReferer
     *
     * @return list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>
     */
    public function filterProductCategoriesByHttpReferer(array $productCategoryStorageTransfers, string $httpReferer): array;

    /**
     * Specification:
     * - Requires `ProductCategoryStorageTransfer.categoryId` to be set.
     * - Returns Product Categories sorted in order from parent to child.
     *
     * @api
     *
     * @param array<int, \Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     *
     * @return list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>
     */
    public function sortProductCategories(array $productCategoryStorageTransfers): array;
}
