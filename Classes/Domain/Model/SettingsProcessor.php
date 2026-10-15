<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\Domain\Model;

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class SettingsProcessor
 *
 * Handles the flexform settings processing for Inlinefluid extension.
 *
 * @author Jörg Hammel <info@sg3w.com>
 */
class SettingsProcessor
{
    /**
     * @var array<string, mixed>
     */
    protected array $xmlArray = [];

    /**
     * sets the flexform data from pi_flexform field
     *
     * @param string|null $piFlexFormData The flexform XML data
     */
    public function setPiFlexFormData(?string $piFlexFormData): void
    {
        if ($piFlexFormData === null || trim($piFlexFormData) === '') {
            $this->xmlArray = [];
            return;
        }

        $this->xmlArray = $this->getFlexFormConverter()->convertFlexFormContentToArray($piFlexFormData);
    }

    /**
     * TYPO3 v14 merged FlexFormService into FlexFormTools. The old class name still
     * works there through a class alias, but that alias will not last forever.
     */
    protected function getFlexFormConverter(): object
    {
        if (method_exists(FlexFormTools::class, 'convertFlexFormContentToArray')) {
            return GeneralUtility::makeInstance(FlexFormTools::class);
        }

        return GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * Retrieves settings as an array based on parent and child XML tag names
     *
     * @param string[] $xmlChildTagNames
     * @return array<int, array<string, mixed>>
     */
    public function getSettingArray(string $xmlParentTagName, array $xmlChildTagNames): array
    {
        $settingsDataRows = [];
        if (isset($this->xmlArray[$xmlParentTagName]) && is_array($this->xmlArray[$xmlParentTagName])) {
            foreach ($this->xmlArray[$xmlParentTagName] as $settingsData) {
                if (!is_array($settingsData)) {
                    continue;
                }
                foreach ($xmlChildTagNames as $xmlChildTagName) {
                    if (isset($settingsData[$xmlChildTagName]) && is_array($settingsData[$xmlChildTagName])) {
                        $row = $settingsData[$xmlChildTagName];
                        $row['type'] = $xmlChildTagName;
                        $settingsDataRows[] = $row;
                    }
                }
            }
        }

        return $settingsDataRows;
    }

    /**
     * Retrieves HTML settings from the flexform
     *
     * @return array{before?: string[], after?: string[]}|null
     */
    public function getHtmlSettings(): ?array
    {
        $htmlSettings = $this->getSettingArray('htmlelements', ['htmlelement']);
        if ($htmlSettings === []) {
            return null;
        }

        $htmlBlock = [];
        foreach ($htmlSettings as $htmlSetting) {
            $position = (int)($htmlSetting['position'] ?? 0) === 0 ? 'before' : 'after';
            $htmlBlock[$position][] = (string)($htmlSetting['content'] ?? '');
        }

        return $htmlBlock;
    }

    /**
     * Retrieves static data provider settings from the flexform
     *
     * @return array<int, array<string, mixed>>
     */
    public function getStaticDataProvider(): array
    {
        return $this->getSettingArray('staticdataproviders', ['json', 'xml', 'yaml', 'serialized', 'csv']);
    }

    /**
     * Retrieves dynamic data provider settings from the flexform
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDynamicDataProvider(): array
    {
        return $this->getSettingArray('dynamicdataproviders', ['php']);
    }

    /**
     * Retrieves file data provider settings from the flexform
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFileDataProvider(): array
    {
        return $this->getSettingArray('filedataprovider', ['file']);
    }

    /**
     * Retrieves variable data provider settings from the flexform
     *
     * @return array<int, array<string, mixed>>
     */
    public function getVariableDataProvider(): array
    {
        return $this->getSettingArray('variableproviders', ['rte', 'text', 'input', 'link']);
    }
}
