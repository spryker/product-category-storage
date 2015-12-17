<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Category\Business\Renderer;

use Generated\Shared\Transfer\LocaleTransfer;
use Spryker\Zed\Category\Persistence\CategoryQueryContainer;
use Orm\Zed\Category\Persistence\SpyCategory;
use Orm\Zed\Category\Persistence\SpyCategoryNode;
use Spryker\Zed\Library\Service\GraphViz;

class CategoryTreeRenderer
{

    const UNKNOWN_CATEGORY = 'Unknown Category';

    /**
     * @var GraphViz
     */
    protected $graph;

    /**
     * @var int
     */
    protected $fontSize = 11;

    /**
     * @var array
     */
    protected $graphDefault = [
        'fontname' => 'Arial',
    ];

    /**
     * @var CategoryQueryContainer
     */
    protected $queryContainer;

    /**
     * @var int
     */
    protected $locale;

    /**
     * @param CategoryQueryContainer $queryContainer
     * @param LocaleTransfer $locale
     */
    public function __construct(CategoryQueryContainer $queryContainer, LocaleTransfer $locale)
    {
        $this->queryContainer = $queryContainer;
        $this->locale = $locale;
    }

    /**
     * @return bool
     */
    public function render()
    {
        $this->graph = new GraphViz(true, $this->graphDefault, 'G', false, true);
        $rootNode = $this->queryContainer->queryRootNode()->findOne();
        if ($rootNode) {
            $this->renderChildren($rootNode);

            return $this->graph->image('svg', 'dot');
        }

        return false;
    }

    /**
     * @param SpyCategoryNode $node
     *
     * @return void
     */
    protected function renderChildren(SpyCategoryNode $node)
    {
        $children = $this->queryContainer
            ->queryFirstLevelChildrenByIdLocale($node->getPrimaryKey(), $this->locale->getIdLocale())
            ->find();

        $deleteLink = '/category-tree/index/delete-node?id=';
        $deleteString = '<br/>click to delete';
        $this->addClosureConnections($node);
        $this->graph->addNode(
            $this->getNodeHash($node),
            [
                'URL' => $deleteLink . $node->getPrimaryKey(),
                'label' => $this->getNodeName($node) . $deleteString,
                'fontsize' => $this->fontSize,
            ]
        );
        foreach ($children as $child) {
            $this->graph->addNode(
                $this->getNodeHash($child),
                [
                    'URL' => $deleteLink . $child->getPrimaryKey(),
                    'label' => $this->getNodeName($child) . $deleteString,
                    'fontsize' => $this->fontSize,
                ]
            );
            $this->graph->addEdge([$this->getNodeHash($node) => $this->getNodeHash($child)]);
            /*
             * Recursive call
             */
            $this->renderChildren($child);
        }
    }

    /**
     * @param SpyCategoryNode $node
     *
     * @return string
     */
    protected function getNodeName(SpyCategoryNode $node)
    {
        return $this->getLocalizedCategoryName($node->getCategory()) . ' (' . $node->getPrimaryKey() . ')';
    }

    /**
     * @param SpyCategoryNode $node
     *
     * @return string
     */
    protected function getNodeHash(SpyCategoryNode $node)
    {
        return md5($this->getNodeName($node));
    }

    /**
     * @param SpyCategoryNode $node
     *
     * @return void
     */
    protected function addClosureConnections(SpyCategoryNode $node)
    {
        $descendantPaths = $this->queryContainer->queryDescendant($node->getPrimaryKey())->find();

        foreach ($descendantPaths as $path) {
            $attributes = ['color' => '#ff0000'];
            $edge = [
                $this->getNodeHash($node) => $this->getNodeHash($path->getDescendantNode()),
            ];
            $this->graph->addEdge($edge, $attributes);
        }
    }

    /**
     * @param SpyCategory $categoryEntity
     *
     * @return string
     */
    protected function getLocalizedCategoryName(SpyCategory $categoryEntity)
    {
        foreach ($categoryEntity->getAttributes() as $attributeEntity) {
            return $attributeEntity->getName();
        }

        return self::UNKNOWN_CATEGORY;
    }

}