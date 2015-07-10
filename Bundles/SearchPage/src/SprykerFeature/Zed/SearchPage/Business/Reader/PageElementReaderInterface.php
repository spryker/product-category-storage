<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace SprykerFeature\Zed\SearchPage\Business\Reader;

use SprykerFeature\Zed\SearchPage\Business\Exception\PageElementDoesNotExistException;
use SprykerFeature\Zed\SearchPage\Persistence\Propel\SpySearchPageElement;

interface PageElementReaderInterface
{

    /**
     * @param int $idPageElement
     *
     * @throws PageElementDoesNotExistException
     *
     * @return SpySearchPageElement
     */
    public function getPageElementById($idPageElement);

}
