<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage\Reader;

interface ProductAbstractCategoryPathReaderInterface
{
    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<int, list<string>>
     */
    public function getProductAbstractCategoryPaths(array $productAbstractIds, string $localeName, string $storeName): array;
}
