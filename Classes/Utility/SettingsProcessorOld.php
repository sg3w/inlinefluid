<?php
namespace  Sg3w\Inlinefluid\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Service\FlexFormService;


/**
 * Class SettingsProcessor
 *
 * @deprecated since 1.0.0
 */
class SettingsProcessorOld
{
    /**
     * returns the projects (stored in the database as flexform XML) as an array for fluid
     */
    public static function projectsArray($projectsXml)
    {
        $flexformService = GeneralUtility::makeInstance(FlexFormService::class);
        $xmlArray = $flexformService->convertFlexFormContentToArray($projectsXml);

        $projects = [];

        if (isset($xmlArray['projects'])) {
            foreach ($xmlArray['projects'] as $projectData) {
                $projects[] = $projectData['projectSingle'];
            }
        }

        return $projects;
    }
}
