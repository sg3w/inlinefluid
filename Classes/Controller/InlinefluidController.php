<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\Controller;

use Psr\Http\Message\ResponseInterface;
use Sg3w\Inlinefluid\DataProvider\DataProviderFactory;
use Sg3w\Inlinefluid\DataProvider\External\AbstractExternalDataProvider;
use Sg3w\Inlinefluid\Domain\Model\SettingsProcessor;
use Sg3w\Inlinefluid\Rendering\FluidSourceRenderer;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class InlinefluidController extends ActionController
{
    protected ?ContentObjectRenderer $contentObject = null;

    /**
     * Collected problems of the single data providers, rendered for logged in
     * backend users by the Show template.
     *
     * @var string[]
     */
    protected array $exceptions = [];

    /**
     * The settings processor is stateful: it keeps the parsed FlexForm of the content
     * element that is currently being rendered. It is registered as a non shared service
     * (see Configuration/Services.yaml) and showAction() hands it a new FlexForm before
     * reading anything from it, so re-using this controller instance for a second
     * content element cannot mix up the two.
     */
    public function __construct(
        protected readonly SettingsProcessor $settingsProcessor
    ) {}

    public function showAction(): ResponseInterface
    {
        $this->exceptions = [];
        $this->contentObject = $this->resolveContentObject();

        if ($this->contentObject === null) {
            $this->view->assign('exceptions', [
                'The current ContentObjectRenderer could not be determined, the content element cannot be rendered.',
            ]);
            return $this->htmlResponse();
        }

        $this->assignDefaultViewVars();

        $this->settingsProcessor->setPiFlexFormData((string)($this->contentObject->data['pi_flexform'] ?? ''));

        $htmlBlocks = $this->settingsProcessor->getHtmlSettings();
        if ($htmlBlocks !== null) {
            $this->view->assign('htmlBlocks', $htmlBlocks);
        }

        $this->assignVariableProviders();
        $this->assignStaticDataProviders();
        $this->assignFileDataProviders();

        $renderingContext = $this->resolveRenderingContext();

        $this->assignDynamicDataProviders($renderingContext);
        $this->assignMediaImages();

        $this->view->assign('exceptions', $this->exceptions);

        $this->view->assign('renderedContent', $this->renderBodytext($renderingContext));

        return $this->htmlResponse();
    }

    protected function assignVariableProviders(): void
    {
        foreach ($this->settingsProcessor->getVariableDataProvider() as $value) {
            if (($value['varname'] ?? '') === '') {
                continue;
            }
            $this->view->assign($value['varname'], $value['data'] ?? null);
        }
    }

    protected function assignStaticDataProviders(): void
    {
        foreach ($this->settingsProcessor->getStaticDataProvider() as $value) {
            try {
                $processor = DataProviderFactory::getStaticDataProvider((string)($value['type'] ?? ''));
                $this->view->assign($value['varname'], $processor->renderData((string)($value['data'] ?? '')));
            } catch (\Throwable $exception) {
                $this->exceptions[] = 'StaticDataProvider Error for variable "'
                    . ($value['varname'] ?? '') . '" : ' . $exception->getMessage();
            }
        }
    }

    protected function assignFileDataProviders(): void
    {
        foreach ($this->settingsProcessor->getFileDataProvider() as $value) {
            try {
                $filePath = GeneralUtility::getFileAbsFileName((string)($value['filename'] ?? ''));
                if ($filePath === '' || !file_exists($filePath)) {
                    throw new \RuntimeException('File not found: ' . ($value['filename'] ?? ''), 1760000001);
                }
                $fileContent = (string)file_get_contents($filePath);
                $processor = DataProviderFactory::getStaticDataProvider((string)($value['format'] ?? ''));
                $this->view->assign($value['varname'], $processor->renderData($fileContent));
            } catch (\Throwable $exception) {
                $this->exceptions[] = 'FileDataProvider Error for variable "'
                    . ($value['varname'] ?? '') . '" : ' . $exception->getMessage();
            }
        }
    }

    protected function assignDynamicDataProviders(?RenderingContextInterface $renderingContext): void
    {
        foreach ($this->settingsProcessor->getDynamicDataProvider() as $value) {
            if (($value['type'] ?? '') !== 'php') {
                continue;
            }
            $className = ltrim((string)($value['providerData'] ?? ''), '\\');
            try {
                if (!class_exists($className)) {
                    throw new \RuntimeException('Class not found: ' . $className, 1760000002);
                }
                $dynamicClass = GeneralUtility::makeInstance($className);
                if (!$dynamicClass instanceof AbstractExternalDataProvider) {
                    throw new \RuntimeException(
                        $className . ' must extend ' . AbstractExternalDataProvider::class,
                        1760000003
                    );
                }
                if ($renderingContext !== null) {
                    $dynamicClass->setRenderingContext($renderingContext);
                }
                $this->view->assign($value['varname'], $dynamicClass->renderData(''));
            } catch (\Throwable $exception) {
                $this->exceptions[] = 'DynamicDataProvider Error for variable "'
                    . ($value['varname'] ?? '') . '" : ' . $exception->getMessage();
            }
        }
    }

    protected function assignMediaImages(): void
    {
        try {
            $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
            $mediaImages = $fileRepository->findByRelation(
                'tt_content',
                'mediaImage',
                (int)($this->contentObject->data['uid'] ?? 0)
            );
            if ($mediaImages !== []) {
                $this->view->assign('mediaImages', $mediaImages);
            }
        } catch (\Throwable $exception) {
            $this->exceptions[] = 'Media Error : ' . $exception->getMessage();
        }
    }

    /**
     * Renders the bodytext of the content element as Fluid template.
     */
    protected function renderBodytext(?RenderingContextInterface $renderingContext): string
    {
        if ($renderingContext === null) {
            $this->exceptions[] = 'The Fluid rendering context is not available, the bodytext was not rendered.';
            return '';
        }

        $content = (string)($this->contentObject->data['bodytext'] ?? '');
        if (trim($content) === '') {
            $content = '<f:debug inline="true">{_all}</f:debug>';
        }

        return FluidSourceRenderer::renderGraceful($renderingContext, $content);
    }

    protected function assignDefaultViewVars(): void
    {
        $addGlobalVariables = GeneralUtility::trimExplode(
            ',',
            (string)($this->settings['addglobalvariables'] ?? ''),
            true
        );
        if ($addGlobalVariables === []) {
            return;
        }

        if (in_array('contentobject', $addGlobalVariables, true)) {
            $this->view->assign('CONTENTOBJECT', $this->contentObject->data);
        }

        if (in_array('tsfe', $addGlobalVariables, true)) {
            $frontendController = $this->resolveFrontendController();
            if ($frontendController !== null) {
                $this->view->assign('TSFE', $frontendController);
            } else {
                $this->exceptions[] = 'The "TSFE" variable is not available: TypoScriptFrontendController '
                    . 'was removed in TYPO3 v14. Use "page", "contentobject" or "tssetup" instead.';
            }
        }

        if (in_array('tssetup', $addGlobalVariables, true)) {
            $typoScriptSetup = $this->resolveTypoScriptSetup();
            if ($typoScriptSetup !== null) {
                $this->view->assign('TSSETUP', $typoScriptSetup);
            } else {
                $this->exceptions[] = 'The "TSSETUP" variable is not available: the TypoScript setup '
                    . 'could not be resolved in the current rendering context.';
            }
        }

        if (in_array('page', $addGlobalVariables, true)) {
            $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
            $this->view->assign('PAGE', $pageRepository->getPage((int)($this->contentObject->data['pid'] ?? 0)));
        }

        if (in_array('request', $addGlobalVariables, true)) {
            $this->view->assign('REQUEST', $this->request->getQueryParams());
        }
    }

    /**
     * The ContentObjectRenderer of the current tt_content record.
     *
     * Since v12 it travels with the request, on v11 it is only known to the
     * Extbase ConfigurationManager.
     */
    protected function resolveContentObject(): ?ContentObjectRenderer
    {
        $contentObject = $this->request->getAttribute('currentContentObject');
        if ($contentObject instanceof ContentObjectRenderer) {
            return $contentObject;
        }

        // TYPO3 v11 and v12
        if (method_exists($this->configurationManager, 'getContentObject')) {
            $contentObject = $this->configurationManager->getContentObject();
            if ($contentObject instanceof ContentObjectRenderer) {
                return $contentObject;
            }
        }

        return null;
    }

    /**
     * The TypoScriptFrontendController, which does not exist anymore on TYPO3 v14.
     */
    protected function resolveFrontendController(): ?object
    {
        if (!class_exists(\TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController::class)) {
            return null;
        }

        // TYPO3 v12 and v13
        $frontendController = $this->request->getAttribute('frontend.controller');
        if (is_object($frontendController)) {
            return $frontendController;
        }

        // TYPO3 v11
        return is_object($GLOBALS['TSFE'] ?? null) ? $GLOBALS['TSFE'] : null;
    }

    /**
     * The full TypoScript setup as plain (dot free) array.
     *
     * @return array<string, mixed>|null
     */
    protected function resolveTypoScriptSetup(): ?array
    {
        $setup = null;

        // TYPO3 v12+
        $frontendTypoScript = $this->request->getAttribute('frontend.typoscript');
        if (is_object($frontendTypoScript)
            && method_exists($frontendTypoScript, 'hasSetup')
            && $frontendTypoScript->hasSetup()
        ) {
            $setup = $frontendTypoScript->getSetupArray();
        } elseif (isset($GLOBALS['TSFE']->tmpl->setup) && is_array($GLOBALS['TSFE']->tmpl->setup)) {
            // TYPO3 v11
            $setup = $GLOBALS['TSFE']->tmpl->setup;
        }

        if (!is_array($setup)) {
            return null;
        }

        return GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($setup);
    }

    protected function resolveRenderingContext(): ?RenderingContextInterface
    {
        if (!method_exists($this->view, 'getRenderingContext')) {
            return null;
        }
        $renderingContext = $this->view->getRenderingContext();

        return $renderingContext instanceof RenderingContextInterface ? $renderingContext : null;
    }
}
