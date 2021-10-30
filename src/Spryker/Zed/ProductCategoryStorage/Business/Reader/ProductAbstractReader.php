<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Business\Reader;

use Spryker\Zed\ProductCategoryStorage\Dependency\Facade\ProductCategoryStorageToCategoryInterface;
use Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageRepositoryInterface;

class ProductAbstractReader implements ProductAbstractReaderInterface
{
    /**
     * @var \Spryker\Zed\ProductCategoryStorage\Dependency\Facade\ProductCategoryStorageToCategoryInterface
     */
    protected $categoryFacade;

    /**
     * @var \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageRepositoryInterface
     */
    protected $productCategoryStorageRepository;

    /**
     * @param \Spryker\Zed\ProductCategoryStorage\Dependency\Facade\ProductCategoryStorageToCategoryInterface $categoryFacade
     * @param \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageRepositoryInterface $productCategoryStorageRepository
     */
    public function __construct(
        ProductCategoryStorageToCategoryInterface $categoryFacade,
        ProductCategoryStorageRepositoryInterface $productCategoryStorageRepository
    ) {
        $this->categoryFacade = $categoryFacade;
        $this->productCategoryStorageRepository = $productCategoryStorageRepository;
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByCategoryIds(array $categoryIds): array
    {
        $relatedCategoryIds = $this->getRelatedCategoryIds($categoryIds);

        return $this->productCategoryStorageRepository->getProductAbstractIdsByCategoryIds($relatedCategoryIds);
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getRelatedCategoryIds(array $categoryIds): array
    {
        $relatedCategoryIds = [];

        foreach ($categoryIds as $idCategory) {
            $relatedCategoryIds = $this->getRelatedCategoryIdsFromCategoryNodes(
                $this->categoryFacade->getAllNodesByIdCategory($idCategory),
                $relatedCategoryIds,
            );
        }

        if ($relatedCategoryIds === []) {
            return [];
        }

        $relatedCategoryIds = array_merge(...$relatedCategoryIds);

        return array_unique($relatedCategoryIds);
    }

    /**
     * @param array<\Generated\Shared\Transfer\NodeTransfer> $nodeTransfers
     * @param array<array<int>> $relatedCategoryIds
     *
     * @return array<array<int>>
     */
    protected function getRelatedCategoryIdsFromCategoryNodes(array $nodeTransfers, array $relatedCategoryIds): array
    {
        foreach ($nodeTransfers as $nodeTransfer) {
            $relatedCategoryIds[] = $this->productCategoryStorageRepository
                ->getAllCategoryIdsByCategoryNodeId($nodeTransfer->getIdCategoryNodeOrFail());
        }

        return $relatedCategoryIds;
    }
}
