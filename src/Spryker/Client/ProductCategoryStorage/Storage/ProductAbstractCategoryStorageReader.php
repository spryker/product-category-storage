<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductCategoryStorage\Storage;

use Generated\Shared\Transfer\ProductAbstractCategoryStorageCollectionTransfer;
use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\Kernel\Locator;
use Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientInterface;
use Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceInterface;
use Spryker\Client\ProductCategoryStorage\ProductCategoryStorageConfig;
use Spryker\Shared\ProductCategoryStorage\ProductCategoryStorageConfig as SharedProductCategoryStorageConfig;

class ProductAbstractCategoryStorageReader implements ProductAbstractCategoryStorageReaderInterface
{
    /**
     * @var \Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientInterface
     */
    protected $storageClient;

    /**
     * @var \Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceInterface
     */
    protected $synchronizationService;

    /**
     * @var array<\Spryker\Client\ProductCategoryStorageExtension\Dependency\Plugin\ProductAbstractCategoryStorageCollectionExpanderPluginInterface>
     */
    protected $productAbstractCategoryStorageCollectionExpanderPlugins;

    /**
     * @param \Spryker\Client\ProductCategoryStorage\Dependency\Client\ProductCategoryStorageToStorageClientInterface $storageClient
     * @param \Spryker\Client\ProductCategoryStorage\Dependency\Service\ProductCategoryStorageToSynchronizationServiceInterface $synchronizationService
     * @param array<\Spryker\Client\ProductCategoryStorageExtension\Dependency\Plugin\ProductAbstractCategoryStorageCollectionExpanderPluginInterface> $productAbstractCategoryStorageCollectionExpanderPlugins
     */
    public function __construct(
        ProductCategoryStorageToStorageClientInterface $storageClient,
        ProductCategoryStorageToSynchronizationServiceInterface $synchronizationService,
        array $productAbstractCategoryStorageCollectionExpanderPlugins
    ) {
        $this->storageClient = $storageClient;
        $this->synchronizationService = $synchronizationService;
        $this->productAbstractCategoryStorageCollectionExpanderPlugins = $productAbstractCategoryStorageCollectionExpanderPlugins;
    }

    /**
     * @param int $idProductAbstract
     * @param string $locale
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer|null
     */
    public function findProductAbstractCategory($idProductAbstract, $locale, string $storeName)
    {
        $productAbstractCategoryStorageData = $this->findStorageData($idProductAbstract, $locale);

        if (!$productAbstractCategoryStorageData) {
            return null;
        }

        $productAbstractCategoryStorageTransfer = (new ProductAbstractCategoryStorageTransfer())->fromArray($productAbstractCategoryStorageData, true);

        $productAbstractCategoryStorageCollectionTransfer = (new ProductAbstractCategoryStorageCollectionTransfer())
            ->addProductAbstractCategoryStorage($productAbstractCategoryStorageTransfer);

        $productAbstractCategoryStorageCollectionTransfer = $this->executeProductAbstractCategoryStorageCollectionExpanderPlugins(
            $productAbstractCategoryStorageCollectionTransfer,
            $locale,
            $storeName,
        );

        $productAbstractCategoryStorageTransfersData = $productAbstractCategoryStorageCollectionTransfer->getProductAbstractCategories()->getArrayCopy();

        return array_pop($productAbstractCategoryStorageTransfersData);
    }

    /**
     * @param int[] $productAbstractIds
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer[]
     */
    public function findBulkProductAbstractCategory(array $productAbstractIds, string $localeName): array
    {
        $productAbstractCategoryStorageData = $this->findBulkStorageData($productAbstractIds, $localeName);
        $productAbstractCategoryStorageData = array_filter($productAbstractCategoryStorageData);

        if (!$productAbstractCategoryStorageData) {
            return [];
        }

        $response = [];
        foreach ($productAbstractCategoryStorageData as $item) {
            $response[] = (new ProductAbstractCategoryStorageTransfer())
                ->fromArray($item, true);
        }

        return $response;
    }

    /**
     * @param int $idProductAbstract
     * @param string $locale
     *
     * @return array|null
     */
    protected function findStorageData(int $idProductAbstract, string $locale): ?array
    {
        if (ProductCategoryStorageConfig::isCollectorCompatibilityMode()) {
            $clientLocatorClassName = Locator::class;
            /** @var \Spryker\Client\Product\ProductClientInterface $productClient */
            $productClient = $clientLocatorClassName::getInstance()->product()->client();
            $collectorData = $productClient->getProductAbstractFromStorageById($idProductAbstract, $locale);
            $categories = [];

            foreach ($collectorData['categories'] as $category) {
                $categories[] = [
                    'category_node_id' => $category['nodeId'],
                    'name' => $category['name'],
                    'url' => $category['url'],
                ];
            }

            return [
                'id_product_abstract' => $idProductAbstract,
                'categories' => $categories,
            ];
        }

        $key = $this->generateKey($idProductAbstract, $locale);
        $productAbstractCategoryStorageData = $this->storageClient->get($key);

        return $productAbstractCategoryStorageData;
    }

    /**
     * @param int[] $productAbstractIds
     * @param string $localeName
     *
     * @return array
     */
    protected function findBulkStorageData(array $productAbstractIds, string $localeName): array
    {
        $storageKeys = [];
        foreach ($productAbstractIds as $idProductAbstract) {
            $storageKeys[] = $this->generateKey($idProductAbstract, $localeName);
        }

        $productAbstractCategoryStorageData = $this->storageClient->getMulti($storageKeys);

        $decodedProductAbstractCategoryStorageData = [];
        foreach ($productAbstractCategoryStorageData as $item) {
            $decodedProductAbstractCategoryStorageData[] = json_decode($item, true);
        }

        return $decodedProductAbstractCategoryStorageData;
    }

    /**
     * @param int|string $idProductAbstract
     * @param string $localeName
     *
     * @return string
     */
    protected function generateKey($idProductAbstract, string $localeName): string
    {
        $synchronizationDataTransfer = new SynchronizationDataTransfer();
        $synchronizationDataTransfer
            ->setLocale($localeName)
            ->setReference($idProductAbstract);

        return $this->synchronizationService
            ->getStorageKeyBuilder(SharedProductCategoryStorageConfig::PRODUCT_ABSTRACT_CATEGORY_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAbstractCategoryStorageCollectionTransfer $productAbstractCategoryStorageCollectionTransfer
     * @param string $localeName
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\ProductAbstractCategoryStorageCollectionTransfer
     */
    protected function executeProductAbstractCategoryStorageCollectionExpanderPlugins(
        ProductAbstractCategoryStorageCollectionTransfer $productAbstractCategoryStorageCollectionTransfer,
        string $localeName,
        string $storeName
    ): ProductAbstractCategoryStorageCollectionTransfer {
        foreach ($this->productAbstractCategoryStorageCollectionExpanderPlugins as $productAbstractCategoryStorageCollectionExpanderPlugin) {
            $productAbstractCategoryStorageCollectionTransfer = $productAbstractCategoryStorageCollectionExpanderPlugin->expand(
                $productAbstractCategoryStorageCollectionTransfer,
                $localeName,
                $storeName,
            );
        }

        return $productAbstractCategoryStorageCollectionTransfer;
    }
}
