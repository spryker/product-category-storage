<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage;

use Orm\Zed\Category\Persistence\SpyCategoryClosureTableQuery;
use Orm\Zed\Category\Persistence\SpyCategoryNodeQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstractLocalizedAttributesQuery;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\ProductCategoryStorage\Dependency\Facade\ProductCategoryStorageToCategoryBridge;
use Spryker\Zed\ProductCategoryStorage\Dependency\Facade\ProductCategoryStorageToEventBehaviorFacadeBridge;
use Spryker\Zed\ProductCategoryStorage\Dependency\Facade\ProductCategoryStorageToStoreFacadeBridge;
use Spryker\Zed\ProductCategoryStorage\Dependency\QueryContainer\ProductCategoryStorageToCategoryQueryContainerBridge;
use Spryker\Zed\ProductCategoryStorage\Dependency\QueryContainer\ProductCategoryStorageToProductCategoryQueryContainerBridge;

/**
 * @method \Spryker\Zed\ProductCategoryStorage\ProductCategoryStorageConfig getConfig()
 */
class ProductCategoryStorageDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const QUERY_CONTAINER_PRODUCT_CATEGORY = 'QUERY_CONTAINER_PRODUCT_CATEGORY';

    /**
     * @var string
     */
    public const QUERY_CONTAINER_CATEGORY = 'QUERY_CONTAINER_CATEGORY';

    /**
     * @var string
     */
    public const FACADE_CATEGORY = 'FACADE_CATEGORY';

    /**
     * @var string
     */
    public const FACADE_EVENT_BEHAVIOR = 'FACADE_EVENT_BEHAVIOR';

    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const PROPEL_QUERY_CATEGORY_NODE = 'PROPEL_QUERY_CATEGORY_NODE';

    /**
     * @var string
     */
    public const PROPEL_QUERY_CATEGORY_CLOSURE_TABLE = 'PROPEL_QUERY_CATEGORY_CLOSURE_TABLE';

    /**
     * @var string
     */
    public const PROPEL_QUERY_PRODUCT_ABSTRACT_LOCALIZED_ATTRIBUTES = 'PROPEL_QUERY_PRODUCT_ABSTRACT_LOCALIZED_ATTRIBUTES';

    /**
     * @var string
     */
    public const PROPEL_QUERY_PRODUCT_CATEGORY = 'PROPEL_QUERY_PRODUCT_CATEGORY';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addEventBehaviorFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addCategoryFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addEventBehaviorFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container): Container
    {
        $container = parent::providePersistenceLayerDependencies($container);

        $container = $this->addProductCategoryQueryContainer($container);
        $container = $this->addCategoryQueryContainer($container);
        $container = $this->addCategoryNodePropelQuery($container);
        $container = $this->addCategoryClosureTablePropelQuery($container);
        $container = $this->addProductAbstractLocalizedAttributesPropelQuery($container);
        $container = $this->addProductCategoryPropelQuery($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryFacade(Container $container): Container
    {
        $container->set(static::FACADE_CATEGORY, function (Container $container) {
            return new ProductCategoryStorageToCategoryBridge($container->getLocator()->category()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventBehaviorFacade(Container $container): Container
    {
        $container->set(static::FACADE_EVENT_BEHAVIOR, function (Container $container) {
            return new ProductCategoryStorageToEventBehaviorFacadeBridge($container->getLocator()->eventBehavior()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new ProductCategoryStorageToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }

    /**
     * @module Category
     *
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryNodePropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_CATEGORY_NODE, $container->factory(function () {
            return SpyCategoryNodeQuery::create();
        }));

        return $container;
    }

    /**
     * @module Category
     *
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryClosureTablePropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_CATEGORY_CLOSURE_TABLE, $container->factory(function () {
            return SpyCategoryClosureTableQuery::create();
        }));

        return $container;
    }

    /**
     * @module Product
     *
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductAbstractLocalizedAttributesPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRODUCT_ABSTRACT_LOCALIZED_ATTRIBUTES, $container->factory(function () {
            return SpyProductAbstractLocalizedAttributesQuery::create();
        }));

        return $container;
    }

    /**
     * @module ProductCategory
     *
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductCategoryPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_PRODUCT_CATEGORY, $container->factory(function () {
            return SpyProductCategoryQuery::create();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductCategoryQueryContainer(Container $container): Container
    {
        $container->set(static::QUERY_CONTAINER_PRODUCT_CATEGORY, function (Container $container) {
            return new ProductCategoryStorageToProductCategoryQueryContainerBridge(
                $container->getLocator()->productCategory()->queryContainer(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCategoryQueryContainer(Container $container): Container
    {
        $container->set(static::QUERY_CONTAINER_CATEGORY, function (Container $container) {
            return new ProductCategoryStorageToCategoryQueryContainerBridge(
                $container->getLocator()->category()->queryContainer(),
            );
        });

        return $container;
    }
}
