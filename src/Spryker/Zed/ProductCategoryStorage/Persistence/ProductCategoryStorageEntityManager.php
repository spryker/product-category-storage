<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Persistence;

use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Orm\Zed\ProductCategoryStorage\Persistence\SpyProductAbstractCategoryStorage;
use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;
use Spryker\Zed\Propel\Persistence\BatchProcessor\ActiveRecordBatchProcessorTrait;

/**
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStoragePersistenceFactory getFactory()
 */
class ProductCategoryStorageEntityManager extends AbstractEntityManager implements ProductCategoryStorageEntityManagerInterface
{
    use ActiveRecordBatchProcessorTrait;

    /**
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function deleteProductAbstractCategoryStorages(array $productAbstractIds): void
    {
        if ($productAbstractIds === []) {
            return;
        }

        /** @var \Propel\Runtime\Collection\ObjectCollection $productAbstractCategoryStorageCollection */
        $productAbstractCategoryStorageCollection = $this->getFactory()
            ->createProductAbstractCategoryStoragePropelQuery()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->find();

        $productAbstractCategoryStorageCollection->delete();
    }

    public function deleteProductAbstractCategoryStorage(int $idProductAbstract, string $storeName, string $localeName): void
    {
        $productAbstractCategoryStorageEntity = $this->getFactory()
            ->createProductAbstractCategoryStoragePropelQuery()
            ->filterByFkProductAbstract($idProductAbstract)
            ->filterByStore($storeName)
            ->filterByLocale($localeName)
            ->findOne();

        if (!$productAbstractCategoryStorageEntity) {
            return;
        }
        $this->remove($productAbstractCategoryStorageEntity);
    }

    public function saveProductAbstractCategoryStorage(
        int $idProductAbstract,
        string $storeName,
        string $localeName,
        ProductAbstractCategoryStorageTransfer $productAbstractCategoryStorageTransfer
    ): void {
        $productAbstractCategoryStorageEntity = $this->getFactory()
            ->createProductAbstractCategoryStoragePropelQuery()
            ->filterByFkProductAbstract($idProductAbstract)
            ->filterByStore($storeName)
            ->filterByLocale($localeName)
            ->findOneOrCreate();

        $productAbstractCategoryStorageEntity->setData($productAbstractCategoryStorageTransfer->toArray());
        $this->persist($productAbstractCategoryStorageEntity);
    }

    public function saveProductAbstractCategoryStorageCollection(array $productAbstractCategoryStorageMap): void
    {
        if ($productAbstractCategoryStorageMap === []) {
            return;
        }

        $existingEntitiesMap = $this->getExistingProductAbstractCategoryStorageEntityMap(array_keys($productAbstractCategoryStorageMap));

        foreach ($productAbstractCategoryStorageMap as $idProductAbstract => $storeMap) {
            foreach ($storeMap as $storeName => $localeMap) {
                foreach ($localeMap as $localeName => $productAbstractCategoryStorageTransfer) {
                    $entity = $existingEntitiesMap[$idProductAbstract][$storeName][$localeName] ?? null;

                    if ($entity === null) {
                        $entity = new SpyProductAbstractCategoryStorage();
                        $entity->setFkProductAbstract($idProductAbstract);
                        $entity->setStore($storeName);
                        $entity->setLocale($localeName);
                    }

                    $entity->setData($productAbstractCategoryStorageTransfer->toArray());
                    $this->persist($entity);
                }
            }
        }
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<int, array<string, array<string, \Orm\Zed\ProductCategoryStorage\Persistence\SpyProductAbstractCategoryStorage>>>
     */
    protected function getExistingProductAbstractCategoryStorageEntityMap(array $productAbstractIds): array
    {
        $entities = $this->getFactory()
            ->createProductAbstractCategoryStoragePropelQuery()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->find();

        $existingEntitiesMap = [];

        foreach ($entities as $entity) {
            $existingEntitiesMap[$entity->getFkProductAbstract()][$entity->getStore()][$entity->getLocale()] = $entity;
        }

        return $existingEntitiesMap;
    }
}
