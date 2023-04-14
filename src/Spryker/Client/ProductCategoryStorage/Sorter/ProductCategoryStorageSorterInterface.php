<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage\Sorter;

interface ProductCategoryStorageSorterInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>
     */
    public function sortProductCategories(array $productCategoryStorageTransfers): array;
}
