<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Persistence;

use Orm\Zed\Category\Persistence\SpyCategoryClosureTableQuery;
use Orm\Zed\Category\Persistence\SpyCategoryNodeQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstractLocalizedAttributesQuery;
use Orm\Zed\ProductCategory\Persistence\SpyProductCategoryQuery;
use Orm\Zed\ProductCategoryStorage\Persistence\SpyProductAbstractCategoryStorageQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;
use Spryker\Zed\ProductCategoryStorage\Persistence\Propel\Mapper\CategoryNodeMapper;
use Spryker\Zed\ProductCategoryStorage\Persistence\Propel\Mapper\ProductAbstractLocalizedAttributesMapper;
use Spryker\Zed\ProductCategoryStorage\Persistence\Propel\Mapper\ProductCategoryMapper;
use Spryker\Zed\ProductCategoryStorage\Persistence\Propel\Mapper\ProductCategoryStorageMapper;
use Spryker\Zed\ProductCategoryStorage\ProductCategoryStorageDependencyProvider;

/**
 * @method \Spryker\Zed\ProductCategoryStorage\ProductCategoryStorageConfig getConfig()
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStorageEntityManagerInterface getEntityManager()
 */
class ProductCategoryStoragePersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Spryker\Zed\ProductCategoryStorage\Dependency\QueryContainer\ProductCategoryStorageToProductCategoryQueryContainerInterface
     */
    public function getProductCategoryQueryContainer()
    {
        return $this->getProvidedDependency(ProductCategoryStorageDependencyProvider::QUERY_CONTAINER_PRODUCT_CATEGORY);
    }

    /**
     * @return \Spryker\Zed\ProductCategoryStorage\Dependency\QueryContainer\ProductCategoryStorageToCategoryQueryContainerInterface
     */
    public function getCategoryQueryContainer()
    {
        return $this->getProvidedDependency(ProductCategoryStorageDependencyProvider::QUERY_CONTAINER_CATEGORY);
    }

    public function createProductAbstractCategoryStoragePropelQuery(): SpyProductAbstractCategoryStorageQuery
    {
        return SpyProductAbstractCategoryStorageQuery::create();
    }

    public function getCategoryNodePropelQuery(): SpyCategoryNodeQuery
    {
        return $this->getProvidedDependency(ProductCategoryStorageDependencyProvider::PROPEL_QUERY_CATEGORY_NODE);
    }

    public function getCategoryClosureTablePropelQuery(): SpyCategoryClosureTableQuery
    {
        return $this->getProvidedDependency(ProductCategoryStorageDependencyProvider::PROPEL_QUERY_CATEGORY_CLOSURE_TABLE);
    }

    public function getProductAbstractLocalizedAttributesPropelQuery(): SpyProductAbstractLocalizedAttributesQuery
    {
        return $this->getProvidedDependency(ProductCategoryStorageDependencyProvider::PROPEL_QUERY_PRODUCT_ABSTRACT_LOCALIZED_ATTRIBUTES);
    }

    public function getProductCategoryPropelQuery(): SpyProductCategoryQuery
    {
        return $this->getProvidedDependency(ProductCategoryStorageDependencyProvider::PROPEL_QUERY_PRODUCT_CATEGORY);
    }

    public function createProductCategoryStorageMapper(): ProductCategoryStorageMapper
    {
        return new ProductCategoryStorageMapper();
    }

    public function createProductCategoryMapper(): ProductCategoryMapper
    {
        return new ProductCategoryMapper();
    }

    public function createProductAbstractLocalizedAttributesMapper(): ProductAbstractLocalizedAttributesMapper
    {
        return new ProductAbstractLocalizedAttributesMapper();
    }

    public function createCategoryNodeMapper(): CategoryNodeMapper
    {
        return new CategoryNodeMapper();
    }
}
