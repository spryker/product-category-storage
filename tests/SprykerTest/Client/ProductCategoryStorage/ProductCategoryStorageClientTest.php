<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\ProductCategoryStorage;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ProductCategoryStorageBuilder;
use Generated\Shared\Transfer\ProductAbstractCategoryStorageTransfer;
use Generated\Shared\Transfer\ProductCategoryStorageTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Spryker\Client\ProductCategoryStorage\ProductCategoryStorageClient;
use Spryker\Client\ProductCategoryStorage\ProductCategoryStorageClientInterface;
use Spryker\Client\Storage\StorageDependencyProvider;
use Spryker\Client\StorageRedis\Plugin\StorageRedisPlugin;
use Spryker\Shared\Kernel\Transfer\Exception\NullValueException;
use Spryker\Shared\ProductCategoryStorage\ProductCategoryStorageConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group ProductCategoryStorage
 * @group ProductCategoryStorageClientTest
 * Add your own group annotations below this line
 */
class ProductCategoryStorageClientTest extends Unit
{
    /**
     * @var int
     */
    protected const INVALID_ID_PRODUCT_ABSTRACT = 1234567890;

    /**
     * @var string
     */
    protected const HTTP_REFERER_CATEGORY_PAGE = 'http://yves.de.spryker.local/en/computer/notebook';

    /**
     * @var string
     */
    protected const HTTP_REFERER_MAIN_PAGE = 'http://yves.de.spryker.local/en';

    /**
     * @var string
     */
    protected const CATEGORY_URL_CAMERA = '/en/camera';

    /**
     * @var string
     */
    protected const CATEGORY_URL_CAMERA_DIGITAL = '/en/camera/digital';

    /**
     * @var string
     */
    protected const CATEGORY_URL_COMPUTER = '/en/computer';

    /**
     * @var string
     */
    protected const CATEGORY_URL_COMPUTER_NOTEBOOK = '/en/computer/notebook';

    /**
     * @var int
     */
    protected const CATEGORY_ID_ROOT = 1;

    /**
     * @var int
     */
    protected const CATEGORY_ID_CAMERA = 2;

    /**
     * @var int
     */
    protected const CATEGORY_ID_CAMERA_DIGITAL = 4;

    /**
     * @var int
     */
    protected const CATEGORY_ID_COMPUTER = 5;

    /**
     * @var int
     */
    protected const CATEGORY_ID_COMPUTER_NOTEBOOK = 6;

    protected const int ID_PRODUCT_ABSTRACT_WITH_CATEGORIES = 2147483000;

    protected const string LOCALE_NAME = 'de_DE';

    protected const string STORE_NAME = 'DE';

    protected const string CATEGORY_NAME_COMPUTER = 'Computer';

    protected const string CATEGORY_NAME_NOTEBOOKS = 'Notebooks';

    protected const string KEY_WITH_CATEGORIES = 'with categories';

    protected const string KEY_WITHOUT_ABSTRACT_PRODUCT = 'without abstract product';

    protected const string KEY_WITHOUT_CATEGORIES = 'without categories';

    /**
     * @var \SprykerTest\Client\ProductCategoryStorage\ProductCategoryStorageClientTester
     */
    protected ProductCategoryStorageClientTester $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(StorageDependencyProvider::PLUGIN_STORAGE, new StorageRedisPlugin());
    }

    public function testFindInvalidProductAbstractCategoryReturnsNull(): void
    {
        // Act
        $returnValue = $this->createProductCategoryStorageClient()
            ->findProductAbstractCategory(static::INVALID_ID_PRODUCT_ABSTRACT, 'de_DE', 'DE');

        // Assert
        $this->assertNull($returnValue);
    }

    public function testFindBulkProductAbstractCategoryReturnsEmptyArray(): void
    {
        // Act
        $productAbstractCategoryStorageTransfers = $this->createProductCategoryStorageClient()
            ->findBulkProductAbstractCategory([static::INVALID_ID_PRODUCT_ABSTRACT], 'de_DE', 'DE');

        // Assert
        $this->assertCount(0, $productAbstractCategoryStorageTransfers);
    }

    public function testExpandProductViewTransfersWithCategoryPathSetsThePathOfEachProductViewAndKeepsTheKeys(): void
    {
        // Arrange
        $storageKey = $this->generateProductAbstractCategoryStorageKey(static::ID_PRODUCT_ABSTRACT_WITH_CATEGORIES);
        $this->tester->getLocator()->storage()->client()->set($storageKey, json_encode($this->createProductAbstractCategoryStorageData()));
        $productViewTransfers = [
            static::KEY_WITH_CATEGORIES => (new ProductViewTransfer())->setIdProductAbstract(static::ID_PRODUCT_ABSTRACT_WITH_CATEGORIES),
            static::KEY_WITHOUT_ABSTRACT_PRODUCT => new ProductViewTransfer(),
            static::KEY_WITHOUT_CATEGORIES => (new ProductViewTransfer())->setIdProductAbstract(static::INVALID_ID_PRODUCT_ABSTRACT),
        ];

        // Act
        $productViewTransfers = $this->createProductCategoryStorageClient()->expandProductViewTransfersWithCategoryPath($productViewTransfers, static::LOCALE_NAME, static::STORE_NAME);

        // Assert
        $this->tester->getLocator()->storage()->client()->delete($storageKey);
        $this->assertSame([static::KEY_WITH_CATEGORIES, static::KEY_WITHOUT_ABSTRACT_PRODUCT, static::KEY_WITHOUT_CATEGORIES], array_keys($productViewTransfers));
        $this->assertSame([static::CATEGORY_NAME_COMPUTER, static::CATEGORY_NAME_NOTEBOOKS], $productViewTransfers[static::KEY_WITH_CATEGORIES]->getCategoryPath());
        $this->assertSame([], $productViewTransfers[static::KEY_WITHOUT_ABSTRACT_PRODUCT]->getCategoryPath());
        $this->assertSame([], $productViewTransfers[static::KEY_WITHOUT_CATEGORIES]->getCategoryPath());
    }

    public function testExpandProductViewTransfersWithCategoryPathReturnsEmptyArrayWithoutProductViews(): void
    {
        // Act
        $productViewTransfers = $this->createProductCategoryStorageClient()->expandProductViewTransfersWithCategoryPath([], static::LOCALE_NAME, static::STORE_NAME);

        // Assert
        $this->assertSame([], $productViewTransfers);
    }

    public function testFilterProductCategoriesByHttpRefererThrowsExceptionWhenUrlIsMissing(): void
    {
        // Arrange
        $productCategoryStorageTransfers = [
            (new ProductCategoryStorageBuilder([
                ProductCategoryStorageTransfer::URL => null,
                ProductCategoryStorageTransfer::CATEGORY_ID => static::CATEGORY_ID_CAMERA,
                ProductCategoryStorageTransfer::PARENT_CATEGORY_IDS => [],
            ]))->build(),
        ];

        // Assert
        $this->expectException(NullValueException::class);

        // Act
        $this->createProductCategoryStorageClient()->filterProductCategoriesByHttpReferer(
            $productCategoryStorageTransfers,
            static::HTTP_REFERER_CATEGORY_PAGE,
        );
    }

    /**
     * @dataProvider productCategoryStorageTransfersForFilteringDataProvider
     *
     * @param string $httpReferer
     * @param list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     * @param list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $expectedProductCategoryStorageTransfers
     *
     * @return void
     */
    public function testFilterProductCategoriesByProductCategoryStorageClientReturnsEqualArray(
        string $httpReferer,
        array $productCategoryStorageTransfers,
        array $expectedProductCategoryStorageTransfers
    ): void {
        // Act
        $productCategoryStorageTransfers = $this->createProductCategoryStorageClient()->filterProductCategoriesByHttpReferer(
            $productCategoryStorageTransfers,
            $httpReferer,
        );

        // Assert
        $this->assertEquals($productCategoryStorageTransfers, $expectedProductCategoryStorageTransfers);
    }

    /**
     * @dataProvider productCategoryStorageTransfersForSortingDataProvider
     *
     * @param list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $productCategoryStorageTransfers
     * @param list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer> $expectedProductCategoryStorageTransfers
     *
     * @return void
     */
    public function testSortProductCategories(array $productCategoryStorageTransfers, array $expectedProductCategoryStorageTransfers): void
    {
        // Act
        $productCategoryStorageTransfers = $this->createProductCategoryStorageClient()->sortProductCategories(
            $productCategoryStorageTransfers,
        );

        // Assert
        foreach ($productCategoryStorageTransfers as $sortedProductCategoryStorageTransferKey => $sortedProductCategoryStorageTransfer) {
            $this->assertSame(
                $expectedProductCategoryStorageTransfers[$sortedProductCategoryStorageTransferKey]->getCategoryId(),
                $sortedProductCategoryStorageTransfer->getCategoryId(),
            );
        }
    }

    /**
     * @return array<array<string|list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>>>
     */
    protected function productCategoryStorageTransfersForFilteringDataProvider(): array
    {
        return [
            [
                static::HTTP_REFERER_CATEGORY_PAGE,
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA_DIGITAL,
                        static::CATEGORY_ID_CAMERA_DIGITAL,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER_NOTEBOOK,
                        static::CATEGORY_ID_COMPUTER_NOTEBOOK,
                        [static::CATEGORY_ID_COMPUTER],
                    ),
                ],
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER_NOTEBOOK,
                        static::CATEGORY_ID_COMPUTER_NOTEBOOK,
                        [static::CATEGORY_ID_COMPUTER],
                    ),
                ],
            ],
            [
                static::HTTP_REFERER_CATEGORY_PAGE,
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA_DIGITAL,
                        static::CATEGORY_ID_CAMERA_DIGITAL,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                ],
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                ],
            ],
            [
                static::HTTP_REFERER_CATEGORY_PAGE,
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA_DIGITAL,
                        static::CATEGORY_ID_CAMERA_DIGITAL,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                ],
                [
                ],
            ],
            [
                static::HTTP_REFERER_CATEGORY_PAGE,
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER_NOTEBOOK,
                        static::CATEGORY_ID_COMPUTER_NOTEBOOK,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                ],
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                ],
            ],
            [
                static::HTTP_REFERER_MAIN_PAGE,
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA_DIGITAL,
                        static::CATEGORY_ID_CAMERA_DIGITAL,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                ],
                [
                ],
            ],
        ];
    }

    /**
     * @return array<array<list<\Generated\Shared\Transfer\ProductCategoryStorageTransfer>>>
     */
    protected function productCategoryStorageTransfersForSortingDataProvider(): array
    {
        return [
            [
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER_NOTEBOOK,
                        static::CATEGORY_ID_COMPUTER_NOTEBOOK,
                        [static::CATEGORY_ID_COMPUTER],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                ],
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER,
                        static::CATEGORY_ID_COMPUTER,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_COMPUTER_NOTEBOOK,
                        static::CATEGORY_ID_COMPUTER_NOTEBOOK,
                        [static::CATEGORY_ID_COMPUTER],
                    ),
                ],
            ],
            [
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA_DIGITAL,
                        static::CATEGORY_ID_CAMERA_DIGITAL,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                ],
                [
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA,
                        static::CATEGORY_ID_CAMERA,
                        [static::CATEGORY_ID_ROOT],
                    ),
                    $this->createProductCategoryStorageTransfer(
                        static::CATEGORY_URL_CAMERA_DIGITAL,
                        static::CATEGORY_ID_CAMERA_DIGITAL,
                        [static::CATEGORY_ID_CAMERA],
                    ),
                ],
            ],
        ];
    }

    /**
     * @param string $url
     * @param int $categoryId
     * @param array<int> $parentCategoryIds
     *
     * @return \Generated\Shared\Transfer\ProductCategoryStorageTransfer
     */
    protected function createProductCategoryStorageTransfer(string $url, int $categoryId, array $parentCategoryIds): ProductCategoryStorageTransfer
    {
        return (new ProductCategoryStorageBuilder([
            ProductCategoryStorageTransfer::URL => $url,
            ProductCategoryStorageTransfer::CATEGORY_ID => $categoryId,
            ProductCategoryStorageTransfer::PARENT_CATEGORY_IDS => $parentCategoryIds,
        ]))->build();
    }

    protected function createProductCategoryStorageClient(): ProductCategoryStorageClientInterface
    {
        return new ProductCategoryStorageClient();
    }

    protected function generateProductAbstractCategoryStorageKey(int $idProductAbstract): string
    {
        $synchronizationDataTransfer = (new SynchronizationDataTransfer())
            ->setReference((string)$idProductAbstract)
            ->setLocale(static::LOCALE_NAME)
            ->setStore(static::STORE_NAME);

        return $this->tester->getLocator()->synchronization()->service()
            ->getStorageKeyBuilder(ProductCategoryStorageConfig::PRODUCT_ABSTRACT_CATEGORY_RESOURCE_NAME)
            ->generateKey($synchronizationDataTransfer);
    }

    /**
     * @return array<string, mixed>
     */
    protected function createProductAbstractCategoryStorageData(): array
    {
        return (new ProductAbstractCategoryStorageTransfer())
            ->setIdProductAbstract(static::ID_PRODUCT_ABSTRACT_WITH_CATEGORIES)
            ->setCategories(new ArrayObject([
                (new ProductCategoryStorageTransfer())
                    ->setCategoryId(static::CATEGORY_ID_COMPUTER)
                    ->setName(static::CATEGORY_NAME_COMPUTER)
                    ->setParentCategoryIds([static::CATEGORY_ID_ROOT]),
                (new ProductCategoryStorageTransfer())
                    ->setCategoryId(static::CATEGORY_ID_COMPUTER_NOTEBOOK)
                    ->setName(static::CATEGORY_NAME_NOTEBOOKS)
                    ->setParentCategoryIds([static::CATEGORY_ID_COMPUTER, static::CATEGORY_ID_ROOT]),
            ]))
            ->toArray();
    }

    public function testFilterProductCategoriesByHttpRefererExcludesNonMatchingUrlsWithSpecialCharacters(): void
    {
        // Arrange
        $productCategoryStorageTransfers = [
            (new ProductCategoryStorageTransfer())
                ->setUrl('/essen/gemüse'),
            (new ProductCategoryStorageTransfer())
                ->setUrl('/startseite/büromöbel'),
        ];

        // Act
        $result = $this->createProductCategoryStorageClient()
            ->filterProductCategoriesByHttpReferer($productCategoryStorageTransfers, 'https://example.com/startseite/b%C3%BCrom%C3%B6bel');

        // Assert
        $this->assertCount(1, $result);
        $this->assertSame('/startseite/büromöbel', $result[0]->getUrl());
    }

    public function testFilterProductCategoriesByHttpRefererIncludesRelatedCategoriesWithSpecialCharacters(): void
    {
        // Arrange
        $productCategoryStorageTransfers = [
            (new ProductCategoryStorageTransfer())
                ->setUrl('/essen')
                ->setCategoryId(1)
                ->setParentCategoryIds([2]),
            (new ProductCategoryStorageTransfer())
                ->setUrl('/essen/gemüse')
                ->setCategoryId(2)
                ->setParentCategoryIds([]),
        ];

        // Act
        $result = $this->createProductCategoryStorageClient()
            ->filterProductCategoriesByHttpReferer($productCategoryStorageTransfers, 'https://example.com/categorie/essen/gem%C3%BCse');

        // Assert
        $this->assertCount(2, $result);
        $this->assertSame('/essen', $result[0]->getUrl());
        $this->assertSame('/essen/gemüse', $result[1]->getUrl());
    }
}
