<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Communication\Plugin\Event\Listener;

use Orm\Zed\Url\Persistence\Map\SpyUrlTableMap;
use Spryker\Zed\Event\Dependency\Plugin\EventBulkHandlerInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Url\Dependency\UrlEvents;

/**
 * @deprecated Use {@link \Spryker\Zed\ProductCategoryStorage\Communication\Plugin\Publisher\CategoryUrl\CategoryUrlWritePublisherPlugin},
 *   {@link \Spryker\Zed\ProductCategoryStorage\Communication\Plugin\Publisher\CategoryUrl\CategoryUrlAndResourceCategorynodeWritePublisherPlugin}
 * instead.
 *
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductCategoryStorage\Communication\ProductCategoryStorageCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductCategoryStorage\Business\ProductCategoryStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\ProductCategoryStorage\ProductCategoryStorageConfig getConfig()
 */
class CategoryUrlStorageListener extends AbstractPlugin implements EventBulkHandlerInterface
{
    /**
     * @api
     *
     * @param array<\Generated\Shared\Transfer\EventEntityTransfer> $eventEntityTransfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $eventEntityTransfers, $eventName)
    {
        if ($eventName === UrlEvents::ENTITY_SPY_URL_DELETE) {
            $categoryNodeIds = $this->getFactory()->getEventBehaviorFacade()->getEventTransferForeignKeys($eventEntityTransfers, SpyUrlTableMap::COL_FK_RESOURCE_CATEGORYNODE);
        } else {
            $categoryNodeIds = $this->getValidCategoryNodeIds($eventEntityTransfers);
        }

        if (!$categoryNodeIds) {
            return;
        }

        $categoryIds = $this->getQueryContainer()->queryCategoryIdsByNodeIds($categoryNodeIds)->find()->getData();
        $relatedCategoryIds = $this->getFacade()->getRelatedCategoryIds($categoryIds);
        $productAbstractIds = $this->getQueryContainer()->queryProductAbstractIdsByCategoryIds($relatedCategoryIds)->find()->getData();

        $this->getFacade()->publish($productAbstractIds);
    }

    /**
     * @param array $eventTransfers
     *
     * @return array
     */
    protected function getValidCategoryNodeIds(array $eventTransfers)
    {
        $validEventTransfers = $this->getFactory()->getEventBehaviorFacade()->getEventTransfersByModifiedColumns(
            $eventTransfers,
            [
                SpyUrlTableMap::COL_URL,
                SpyUrlTableMap::COL_FK_RESOURCE_CATEGORYNODE,
            ],
        );

        return $this->getFactory()->getEventBehaviorFacade()->getEventTransferForeignKeys($validEventTransfers, SpyUrlTableMap::COL_FK_RESOURCE_CATEGORYNODE);
    }
}
