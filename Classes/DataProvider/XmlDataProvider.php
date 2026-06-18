<?php
namespace  Sg3w\Inlinefluid\DataProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class XmlDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString) :mixed
    {
        $xmlObject = simplexml_load_string($dataString, "SimpleXMLElement", LIBXML_NOCDATA);
        return json_decode(json_encode($xmlObject), true);
    }
}