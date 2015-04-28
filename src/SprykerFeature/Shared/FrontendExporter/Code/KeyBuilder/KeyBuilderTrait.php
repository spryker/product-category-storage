<?php

namespace SprykerFeature\Shared\FrontendExporter\Code\KeyBuilder;

use SprykerEngine\Shared\Dto\LocaleDto;
use SprykerEngine\Shared\Kernel\Store;

trait KeyBuilderTrait
{
    /**
     * @var string
     */
    protected $keySeparator = '.';

    /**
     * @param mixed $data
     * @param LocaleDto $locale
     *
     * @return string
     */
    public function generateKey($data, LocaleDto $locale)
    {
        $keyParts = [
            Store::getInstance()->getStoreName(),
            $locale->getLocaleName(),
            $this->getBundleName(),
            $this->buildKey($data)
        ];

        return $this->escapeKey(implode($this->keySeparator, $keyParts));
    }


    /**
     * @param string $key
     *
     * @return string
     */
    protected function escapeKey($key)
    {
        $charsToReplace = array('"', "'", ' ', "\0", "\n", "\r");

        return str_replace($charsToReplace, '-', mb_strtolower(trim($key)));
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    abstract protected function buildKey($data);

    /**
     * @return string
     */
    abstract public function getBundleName();
}
