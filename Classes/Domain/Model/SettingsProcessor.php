<?php

namespace Sg3w\Inlinefluid\Domain\Model;

use Sg3w\Inlinefluid\Utility\SettingsProcessorOld;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/**
 * Class SettingsProcessor
 *
 * Handles the flexform settings processing for Inlinefluid extension.
 *
 * @author Jörg Hammel <info@sg3w.com>
 *
 */
class SettingsProcessor extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    protected $xmlArray = [];

    /**
     * sets the flexform data from pi_flexform field
     *
     * @param string $piFlexFormData The flexform XML data
     * @return void
     */
    public function setPiFlexFormData($piFlexFormData): void
    {
        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->xmlArray = $flexformService->convertFlexFormContentToArray($piFlexFormData);
    }

    /**
     * Retrieves settings as an array based on parent and child XML tag names
     *
     *
     * @param string $xmlParentTagName
     * @param array $xmlChildTagNames
     * @return array
     */
    public function getSettingArray(string $xmlParentTagName, array $xmlChildTagNames)
    {
        $settingsDataRows = [];
        if (isset($this->xmlArray[$xmlParentTagName]) && is_array($this->xmlArray[$xmlParentTagName])) {
            foreach ($this->xmlArray[$xmlParentTagName] as $settingsData) {
                foreach ($xmlChildTagNames as $xmlChildTagName) {
                    if (isset($settingsData[$xmlChildTagName])) {
                        $settingsData[$xmlChildTagName]['type'] = $xmlChildTagName;
                        $settingsDataRows[] = $settingsData[$xmlChildTagName];
                    }
                }
            }
        }
        return $settingsDataRows;
    }



    /**
     * Retrieves HTML settings from the flexform
     *
     * @return array|null
     */
    public function getHtmlSettings(): ?array
    {
        $htmlSettings = $this->getSettingArray('htmlelements', ['htmlelement']);
        if (count($htmlSettings) > 0) {
            $htmlBlock = [];
            foreach ($htmlSettings as $key => $value) {
                if ((int )$htmlSettings[$key]['position'] == 0) {
                    $htmlBlock['before'][] = $htmlSettings[$key]['content'];
                } else {
                    $htmlBlock['after'][] = $htmlSettings[$key]['content'];
                }
            }
            return $htmlBlock;
        }
        return null;
    }

    /**
     * Retrieves static data provider settings from the flexform
     *
     * @return array
     */
    public function getStaticDataProvider()
    {
        return $this->getSettingArray('staticdataproviders', ['json', 'xml', 'yaml','serialized','csv']);
    }

    /**
     * Retrieves static data provider settings from the flexform
     *
     * @return array
     */
    public function getDynamicDataProvider()
    {
        return $this->getSettingArray('dynamicdataproviders', ['php']);
    }

    /**
     * Retrieves static data provider settings from the flexform
     *
     * @return array
     */
    public function getFileDataProvider()
    {
        return $this->getSettingArray('filedataprovider', ['file']);
    }

    /**
     * Retrieves variable data provider settings from the flexform
     *
     * @return array
     */
    public function getVariableDataProvider()
    {
        return $this->getSettingArray('variableproviders', ['rte', 'text', 'input', 'link']);
    }
}