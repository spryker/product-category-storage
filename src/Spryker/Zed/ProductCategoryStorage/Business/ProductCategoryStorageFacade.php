<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Business;

use Generated\Shared\Transfer\FilterTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\ProductCategoryStorage\Business\ProductCategoryStorageBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageEntityManagerInterface getEntityManager()
 */
class ProductCategoryStorageFacade extends AbstractFacade implements ProductCategoryStorageFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function publish(array $productAbstractIds)
    {
        $this->getFactory()->createProductCategoryStorageWriter()->writeCollection($productAbstractIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function unpublish(array $productAbstractIds)
    {
        $this->getFactory()->createProductCategoryStorageDeleter()->deleteCollection($productAbstractIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getRelatedCategoryIds(array $categoryIds)
    {
        return $this->getFactory()->createProductAbstractReader()->getRelatedCategoryIds($categoryIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryStoreEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryStoreEventsWriter()
            ->writeCollectionByCategoryStoreEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryStorePublishingEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryStoreEventsWriter()
            ->writeCollectionByCategoryStorePublishingEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCollectionByCategoryStoreEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageDeleter()
            ->deleteCollectionByCategoryStoreEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryAttributeEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryAttributeEventsWriter()
            ->writeCollectionByCategoryAttributeEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryAttributeNameEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryAttributeEventsWriter()
            ->writeCollectionByCategoryAttributeNameEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryNodeEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryNodeEventsWriter()
            ->writeCollectionByCategoryNodeEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryEventsWriter()
            ->writeCollectionByCategoryEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryIsActiveAndCategoryKeyEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryEventsWriter()
            ->writeCollectionByCategoryIsActiveAndCategoryKeyEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryUrlEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryUrlEventsWriter()
            ->writeCollectionByCategoryUrlEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryUrlAndResourceCategorynodeEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByCategoryUrlEventsWriter()
            ->writeCollectionByCategoryUrlAndResourceCategorynodeEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByProductCategoryPublishingEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByProductCategoryEventsWriter()
            ->writeCollectionByProductCategoryPublishingEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByProductCategoryEvents(array $eventEntityTransfers): void
    {
        $this->getFactory()
            ->createProductCategoryStorageByProductCategoryEventsWriter()
            ->writeCollectionByProductCategoryEvents($eventEntityTransfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $offset
     * @param int $limit
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\SynchronizationDataTransfer>
     */
    public function getProductAbstractCategoryStorageSynchronizationDataTransfersByProductAbstractIds(
        int $offset,
        int $limit,
        array $productAbstractIds
    ): array {
        return $this->getRepository()
            ->getProductAbstractCategoryStorageSynchronizationDataTransfersByProductAbstractIds($offset, $limit, $productAbstractIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryTransfer>
     */
    public function getProductCategoryTransfersByFilter(FilterTransfer $filterTransfer): array
    {
        return $this->getRepository()
            ->getProductCategoryTransfersByFilter($filterTransfer);
    }
}
