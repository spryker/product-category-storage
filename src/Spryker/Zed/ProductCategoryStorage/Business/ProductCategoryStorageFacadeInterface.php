<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Business;

use Generated\Shared\Transfer\FilterTransfer;

interface ProductCategoryStorageFacadeInterface
{
    /**
     * Specification:
     * - Queries all productCategories with the given productAbstractIds
     * - Stores data as json encoded to storage table
     * - Sends a copy of data to queue based on module config
     *
     * @api
     *
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function publish(array $productAbstractIds);

    /**
     * Specification:
     * - Finds and deletes productCategories storage entities with the given productAbstractIds
     * - Sends delete message to queue based on module config
     *
     * @api
     *
     * @deprecated Will be removed in the next major without replacement.
     *
     * @param array<int> $productAbstractIds
     *
     * @return void
     */
    public function unpublish(array $productAbstractIds);

    /**
     * Specification:
     * - Returns related category ids with the given categoryIds
     *
     * @api
     *
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getRelatedCategoryIds(array $categoryIds);

    /**
     * Specification:
     * - Extracts category store IDs from the $eventTransfers created by category store events.
     * - Finds all category IDs related to category store IDs.
     * - Queries all product abstract IDs related to categories.
     * - Queries all productCategories with the given productAbstractIds.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryStoreEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category store IDs from the $eventTransfers created by category store events.
     * - Finds all category IDs related to category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Queries all productCategories with the given productAbstractIds.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryStorePublishingEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category store IDs from the $eventTransfers created by category store events.
     * - Finds all category IDs related to category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Deletes entities from `spy_product_abstract_category_storage` based on product abstract IDs.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function deleteCollectionByCategoryStoreEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category attribute events.
     * - Finds all related category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryAttributeEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Filter `eventEntityTransfers` by modified `name` column.
     * - Extracts category IDs from the $eventTransfers.
     * - Finds all related category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryAttributeNameEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category node events.
     * - Finds all related category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryNodeEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category IDs from the $eventTransfers created by category events.
     * - Finds all related category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Filter `eventEntityTransfers` by modified `isActive` and `categoryKey` columns.
     * - Extracts category IDs from the $eventTransfers created by category events.
     * - Finds all related category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryIsActiveAndCategoryKeyEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts category node IDs from the $eventTransfers created by category url events.
     * - Finds all category IDs related to category node IDs.
     * - Finds all related category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryUrlEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Filter `eventEntityTransfers` by modified `url` and `resourceCategorynode` columns.
     * - Extracts category IDs from the $eventTransfers created by category events.
     * - Finds all category IDs related to category IDs.
     * - Queries all product abstract IDs related to categories.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByCategoryUrlAndResourceCategorynodeEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts product abstract IDs from $eventTransfers created by product category publishing events.
     * - Finds all product categories related to product abstract IDs.
     * - Finds all product abstract localized attributes related to product abstract IDs.
     * - Finds a collection of product abstract category storages related to product abstract IDs.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByProductCategoryPublishingEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Extracts product abstract IDs from $eventTransfers created by product category events.
     * - Finds all product categories related to product abstract IDs.
     * - Finds all product abstract localized attributes related to product abstract IDs.
     * - Finds a collection of product abstract category storages related to product abstract IDs.
     * - Stores data as json encoded to storage table.
     * - Sends a copy of data to queue based on module config.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     *
     * @return void
     */
    public function writeCollectionByProductCategoryEvents(array $eventEntityTransfers): void;

    /**
     * Specification:
     * - Retrieves a product abstract category storage collection according to provided offset, limit and `productAbstractIds`.
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
    ): array;

    /**
     * Specification:
     * - Retrieves product categories by provided filter.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryTransfer>
     */
    public function getProductCategoryTransfersByFilter(FilterTransfer $filterTransfer): array;
}
