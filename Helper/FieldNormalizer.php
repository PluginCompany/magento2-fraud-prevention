<?php
/**
 * Copyright (c) 2014-2026 Plugin Company
 *
 * This file is part of Fraud Prevention for Magento 2, released under the MIT License.
 * See the LICENSE file in the project root.
 */
namespace PluginCompany\FraudPrevention\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Locale\Resolver;

class FieldNormalizer extends AbstractHelper
{
    /**
     * @var Resolver
     */
    private $scopeResolver;
    private $defaultLocale;

    /**
     * Data constructor.
     * @param Context $context
     * @param Resolver $scopeResolver
     */
    public function __construct(
        Context $context,
        Resolver $scopeResolver
    ) {
        parent::__construct($context);
        $this->scopeResolver = $scopeResolver;
        $this->defaultLocale = setlocale(LC_CTYPE, 0);
    }

    public function normalizePhone($text)
    {
        $text = (string)$text;
        return preg_replace('/[^\d]/u', '', $text);
    }

    public function normalizeName($text)
    {
        return preg_replace('/[^\p{L}]/u', '', $this->normalizeText($text));
    }

    public function normalizeText($text)
    {
        $text = (string)$text;
        $text = $this->removeSpecialCharactersAndWhiteSpace($text);
        $text = $this->translit($text);
        return $text;
    }

    private function removeSpecialCharactersAndWhiteSpace($text)
    {
        return preg_replace('/[^\p{L}\d]/u', '', $text);
    }

    private function translit($text)
    {
        $this->switchToNormalizationLocale();
        if(function_exists('transliterator_transliterate')) {
            $text = str_replace(' ' , '', transliterator_transliterate('Russian-Latin/BGN;Any-Latin;Latin-ASCII;', $text));
        }
        $textArray = $this->convertStringToArray($text);
        $result = "";
        foreach($textArray as $key => $character)
        {
            $converted = iconv("UTF-8", "ASCII//TRANSLIT//IGNORE", $character);
            if($converted == '?') {
                $converted = $character;
            }
            $result .= $converted;
        }
        $this->revertToOriginalLocale();
        return $result;
    }

    private function convertStringToArray($text)
    {
        return preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY);
    }

    private function switchToNormalizationLocale()
    {
        $locale = setlocale(LC_CTYPE, $this->getDefaultLocale() . '.UTF-8');
        if(!$locale) {
            setlocale(LC_CTYPE, 'en_US.UTF-8');
        }
        return $this;
    }

    private function getDefaultLocale()
    {
        return $this->scopeResolver->getDefaultLocale();
    }

    private function revertToOriginalLocale()
    {
        setlocale(LC_CTYPE, $this->defaultLocale);
        return $this;
    }

}