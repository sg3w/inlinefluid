<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

use TYPO3\CMS\Core\Utility\CsvUtility;

class CsvDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString): mixed
    {
        return CsvUtility::csvToArray($dataString);
    }
}
