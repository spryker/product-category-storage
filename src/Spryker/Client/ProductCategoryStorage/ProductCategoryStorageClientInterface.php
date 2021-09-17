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
}
