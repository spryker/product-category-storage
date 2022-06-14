<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductCategoryStorage\Persistence;

use Generated\Shared\Transfer\CategoryNodeAggregationTransfer;
use Generated\Shared\Transfer\FilterTransfer;
use Orm\Zed\Category\Persistence\Map\SpyCategoryAttributeTableMap;
use Orm\Zed\Category\Persistence\Map\SpyCategoryClosureTableTableMap;
use Orm\Zed\Category\Persistence\Map\SpyCategoryNodeTableMap;
use Orm\Zed\Category\Persistence\Map\SpyCategoryTableMap;
use Orm\Zed\Category\Persistence\SpyCategoryNodeQuery;
use Orm\Zed\Locale\Persistence\Map\SpyLocaleTableMap;
use Orm\Zed\ProductCategory\Persistence\Map\SpyProductCategoryTableMap;
use Orm\Zed\ProductCategoryStorage\Persistence\Map\SpyProductAbstractCategoryStorageTableMap;
use Orm\Zed\Store\Persistence\Map\SpyStoreTableMap;
use Orm\Zed\Url\Persistence\Map\SpyUrlTableMap;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Synchronization\Persistence\Propel\Formatter\SynchronizationDataTransferObjectFormatter;

/**
 * @method \Spryker\Zed\ProductCategoryStorage\Persistence\ProductCategoryStoragePersistenceFactory getFactory()
 */
class ProductCategoryStorageRepository extends AbstractRepository implements ProductCategoryStorageRepositoryInterface
{
    /**
     * @var string
     */
    protected const COL_FK_CATEGORY = 'fk_category';

    /**
     * @var string
     */
    protected const COL_BOOSTED_DEPTH = 'boostedDepth';

    /**
     * @var int
     */
    protected const DEPTH_TO_BOOST = 0;

    /**
     * @module Url
     * @module Store
     * @module Locale
     *
     * @return array<\Generated\Shared\Transfer\CategoryNodeAggregationTransfer>
     */
    public function getAllCategoryNodeAggregationsOrderedByDescendant(): array
    {
        $categoryNodeQuery = $this->getFactory()
            ->getCategoryNodePropelQuery()
            ->addJoin(
                SpyCategoryNodeTableMap::COL_ID_CATEGORY_NODE,
                SpyUrlTableMap::COL_FK_RESOURCE_CATEGORYNODE,
                Criteria::LEFT_JOIN,
            )
            ->where(SpyUrlTableMap::COL_FK_LOCALE . ' = ' . SpyCategoryAttributeTableMap::COL_FK_LOCALE);

        $categoryNodeQuery = $this->addOrderByDepthDescendantToCategoryNodeQuery($categoryNodeQuery);

        $categoryNodeQuery
            ->useClosureTableQuery()
                ->orderByFkCategoryNodeDescendant(Criteria::DESC)
                ->filterByDepth(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->useCategoryQuery()
                ->useSpyCategoryStoreQuery(null, Criteria::LEFT_JOIN)
                    ->joinWithSpyStore()
                ->endUse()
                ->useAttributeQuery(null, Criteria::LEFT_JOIN)
                    ->joinWithLocale()
                ->endUse()
            ->endUse();

        $categoryNodeQuery->filterByIsRoot(false);

        $categoryNodeQuery->orderBy(SpyCategoryNodeTableMap::COL_NODE_ORDER, Criteria::DESC);

        $categoryNodeQuery
            ->withColumn(SpyCategoryNodeTableMap::COL_ID_CATEGORY_NODE, CategoryNodeAggregationTransfer::ID_CATEGORY_NODE)
            ->withColumn(SpyCategoryClosureTableTableMap::COL_FK_CATEGORY_NODE_DESCENDANT, CategoryNodeAggregationTransfer::ID_CATEGORY_NODE_DESCENDANT)
            ->withColumn(SpyCategoryNodeTableMap::COL_FK_CATEGORY, CategoryNodeAggregationTransfer::ID_CATEGORY)
            ->withColumn(SpyCategoryAttributeTableMap::COL_NAME, CategoryNodeAggregationTransfer::NAME)
            ->withColumn(SpyUrlTableMap::COL_URL, CategoryNodeAggregationTransfer::URL)
            ->withColumn(SpyLocaleTableMap::COL_LOCALE_NAME, CategoryNodeAggregationTransfer::LOCALE)
            ->withColumn(SpyStoreTableMap::COL_NAME, CategoryNodeAggregationTransfer::STORE);

        $categoryNodeQuery->select([
            CategoryNodeAggregationTransfer::ID_CATEGORY_NODE,
            CategoryNodeAggregationTransfer::ID_CATEGORY_NODE_DESCENDANT,
            CategoryNodeAggregationTransfer::ID_CATEGORY,
            CategoryNodeAggregationTransfer::NAME,
            CategoryNodeAggregationTransfer::URL,
            CategoryNodeAggregationTransfer::LOCALE,
            CategoryNodeAggregationTransfer::STORE,
        ]);

        return $this->getFactory()
            ->createCategoryNodeMapper()
            ->mapCategoryNodesToCategoryNodeAggregationTransfers($categoryNodeQuery->find(), []);
    }

    /**
     * @return array<int>
     */
    public function getAllCategoryNodeIds(): array
    {
        return $this->getFactory()
            ->getCategoryNodePropelQuery()
            ->orderBy(SpyCategoryNodeTableMap::COL_NODE_ORDER, Criteria::DESC)
            ->select([SpyCategoryNodeTableMap::COL_ID_CATEGORY_NODE])
            ->find()
            ->toArray();
    }

    /**
     * @param int $idCategoryNode
     *
     * @return array<int>
     */
    public function getAllCategoryIdsByCategoryNodeId(int $idCategoryNode): array
    {
        return $this->getFactory()
            ->getCategoryClosureTablePropelQuery()
            ->where(SpyCategoryClosureTableTableMap::COL_FK_CATEGORY_NODE . ' = ?', $idCategoryNode)
            ->_or()
            ->where(SpyCategoryClosureTableTableMap::COL_FK_CATEGORY_NODE_DESCENDANT . ' = ?', $idCategoryNode)
            ->joinDescendantNode()
            ->withColumn(SpyCategoryNodeTableMap::COL_FK_CATEGORY, static::COL_FK_CATEGORY)
            ->select([static::COL_FK_CATEGORY])
            ->find()
            ->getData();
    }

    /**
     * @module Locale
     *
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductAbstractLocalizedAttributesTransfer>
     */
    public function getProductAbstractLocalizedAttributes(array $productAbstractIds): array
    {
        $productAbstractLocalizedAttributesEntities = $this->getFactory()
            ->getProductAbstractLocalizedAttributesPropelQuery()
            ->joinWithLocale()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->find();

        return $this->getFactory()
            ->createProductAbstractLocalizedAttributesMapper()
            ->mapProductAbstractLocalizedAttributesEntitiesToProductAbstractLocalizedAttributesTransfers(
                $productAbstractLocalizedAttributesEntities,
                [],
            );
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryTransfer>
     */
    public function getProductCategoryWithCategoryNodes(array $productAbstractIds): array
    {
        $productCategoryQuery = $this->getFactory()
            ->getProductCategoryPropelQuery()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->innerJoinSpyCategory()
            ->addAnd(
                SpyCategoryTableMap::COL_IS_ACTIVE,
                true,
                Criteria::EQUAL,
            )
            ->joinWithSpyCategory()
            ->joinWith('SpyCategory.Node')
            ->orderByProductOrder();

        return $this->getFactory()
            ->createProductCategoryMapper()
            ->mapProductCategoryEntitiesToProductCategoryTransfers($productCategoryQuery->find(), []);
    }

    /**
     * @param array<int> $productAbstractIds
     *
     * @return array<array<array<\Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer>>>
     */
    public function getMappedProductAbstractCategoryStorages(array $productAbstractIds): array
    {
        $productAbstractCategoryStorageEntities = $this
            ->getFactory()
            ->createProductAbstractCategoryStoragePropelQuery()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->find();

        return $this->getFactory()
            ->createProductCategoryStorageMapper()
            ->mapProductAbstractCategoryStorageEntitiesToProductAbstractCategoryStorageTransfers(
                $productAbstractCategoryStorageEntities,
                [],
            );
    }

    /**
     * @param array<int> $categoryIds
     *
     * @return array<int>
     */
    public function getProductAbstractIdsByCategoryIds(array $categoryIds): array
    {
        return $this->getFactory()
            ->getProductCategoryPropelQuery()
            ->filterByFkCategory_In($categoryIds)
            ->select(SpyProductCategoryTableMap::COL_FK_PRODUCT_ABSTRACT)
            ->find()
            ->getData();
    }

    /**
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
        $filterTransfer = $this->createFilterTransfer(
            $offset,
            $limit,
            SpyProductAbstractCategoryStorageTableMap::COL_ID_PRODUCT_ABSTRACT_CATEGORY_STORAGE,
        );

        $query = $this->getFactory()->createProductAbstractCategoryStoragePropelQuery();

        if ($productAbstractIds) {
            $query->filterByFkProductAbstract_In($productAbstractIds);
        }

        return $this->buildQueryFromCriteria($query, $filterTransfer)
            ->setFormatter(SynchronizationDataTransferObjectFormatter::class)
            ->find();
    }

    /**
     * @param \Generated\Shared\Transfer\FilterTransfer $filterTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductCategoryTransfer>
     */
    public function getProductCategoryTransfersByFilter(FilterTransfer $filterTransfer): array
    {
        $query = $this->getFactory()->getProductCategoryPropelQuery();

        $productCategoryEnteties = $this->buildQueryFromCriteria($query, $filterTransfer)
            ->setFormatter(ModelCriteria::FORMAT_OBJECT)
            ->find();

        return $this->getFactory()
            ->createProductCategoryMapper()
            ->mapProductCategoryEntitiesToProductCategoryTransfers($productCategoryEnteties, []);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param string $orderByColumnName
     *
     * @return \Generated\Shared\Transfer\FilterTransfer
     */
    protected function createFilterTransfer(int $offset, int $limit, string $orderByColumnName): FilterTransfer
    {
        return (new FilterTransfer())
            ->setOrderBy($orderByColumnName)
            ->setOffset($offset)
            ->setLimit($limit);
    }

    /**
     * @param \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery $categoryNodeQuery
     *
     * @return \Orm\Zed\Category\Persistence\SpyCategoryNodeQuery
     */
    protected function addOrderByDepthDescendantToCategoryNodeQuery(SpyCategoryNodeQuery $categoryNodeQuery): SpyCategoryNodeQuery
    {
        $depthToBoostClause = sprintf(
            '(CASE WHEN %s = %s THEN 1 ELSE 0 END)',
            SpyCategoryClosureTableTableMap::COL_DEPTH,
            static::DEPTH_TO_BOOST,
        );

        $categoryNodeQuery
            ->withColumn($depthToBoostClause, static::COL_BOOSTED_DEPTH)
            ->orderBy(static::COL_BOOSTED_DEPTH, Criteria::DESC)
            ->useClosureTableQuery()
                ->orderByDepth(Criteria::ASC)
            ->endUse();

        return $categoryNodeQuery;
    }
}
