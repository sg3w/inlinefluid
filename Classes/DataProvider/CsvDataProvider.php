<?php
namespace  Sg3w\Inlinefluid\DataProvider;

use TYPO3\CMS\Core\Utility\CsvUtility;
class CsvDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString) :mixed
    {
        $csv = new CsvUtility();
        return $csv->csvToArray($dataString);
    }
}