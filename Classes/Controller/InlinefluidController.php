<?php

namespace Sg3w\Inlinefluid\Controller;

use Psr\Http\Message\ResponseInterface;
use Sg3w\Inlinefluid\DataProvider\External\AbstractExternalDataProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Configuration\TypoScript\ConditionMatching\ConditionMatcher;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\View\ViewInterface;

use Sg3w\Inlinefluid\Domain\Model\SettingsProcessor;
use Sg3w\Inlinefluid\DataProvider\DataProviderFactory;
use Sg3w\Inlinefluid\DataProvider\External\AbstarctExtrnalDataProvider;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Resource\FileRepository;

class InlinefluidController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    private $contentObject = null;

    /**
     * action show
     *
     * @return void
     */
    public function showAction(): ResponseInterface
    {
        $exceptions = [];
        $this->contentObject = $this->request->getAttribute('currentContentObject');
        $this->assignDefaultViewVars();


        $settingProcessor = GeneralUtility::makeInstance(SettingsProcessor::class);
        $settingProcessor->setPiFlexFormData($this->contentObject->data['pi_flexform']);
        $htmlBlocks = $settingProcessor->getHtmlSettings();
        if(!is_null($htmlBlocks)){
            $this->view->assign('htmlBlocks',$htmlBlocks );
        }


        $variablesProvider = $settingProcessor->getVariableDataProvider();
        foreach ($variablesProvider as $key => $value) {
            $this->view->assign($value['varname'], $value['data']);
        }


        $staticProvider = $settingProcessor->getStaticDataProvider();
        foreach ($staticProvider as $key => $value) {
            try{
                $processor = DataProviderFactory::getStaticDataProvider($value['type']);
                $result = $processor->renderData($value['data']);
                $this->view->assign($value['varname'], $result);
            }catch (\Exception $exception){
                $exceptions[] = 'StaticDataProvider Error for variable "'.$value['varname'].'" : '.$exception->getMessage();
            }
        }

        $fileDataProvider = $settingProcessor->getFileDataProvider();
        foreach ($fileDataProvider as $key => $value) {
            try{
                $filePath = GeneralUtility::getFileAbsFileName($value['filename']);
                if(file_exists($filePath)){
                    $fileContent = file_get_contents($filePath);
                }else{
                    throw new \Exception('File not found: '.$value['filename']);
                }
                $processor = DataProviderFactory::getStaticDataProvider($value['format']);
                $result = $processor->renderData($fileContent);

                $this->view->assign($value['varname'], $result);
            }catch (\Exception $exception){
                $exceptions[] = 'FileDataProvider Error for variable "'.$value['varname'].'" : '.$exception->getMessage();
            }

        }
        $renderingContext = $this->view->getRenderingContext();

        $dynamicDataProvider = $settingProcessor->getDynamicDataProvider();
        //debug($dynamicDataProvider);
        foreach ($dynamicDataProvider as $key => $value) {
            if($value['type']==='php'){

                try{
                    //$dynamicClass = GeneralUtility::makeInstance($value['providerData']);
                    if(!class_exists($value['providerData'])){
                        throw new \Exception('Class not found: '.$value['providerData']);
                    }


                    $dynamicClass = new $value['providerData']();
                    if($dynamicClass instanceof AbstractExternalDataProvider){
                        $dynamicClass->setRenderingContext($renderingContext);
                       // $dynamicClass->setControllerContext($renderingContext->getControllerContext());
                        $result = $dynamicClass->renderData('');
                        //debug($result);
                        $this->view->assign($value['varname'], $result);
                    }
                }catch (\Exception $exception){
                    $exceptions[] = 'DynamicDataProvider Error for variable "'.$value['varname'].'" : '.$exception->getMessage();
                  //  $exceptions[] = 'DynamicDataProvider Error for variable "'.$value['varname'].'" : '.$exception->getMessage();
                }
            }

        }


        $fileRepository = GeneralUtility::makeInstance(FileRepository::class);
        $mediaImages = $fileRepository->findByRelation('tt_content', 'mediaImage', $this->contentObject->data['uid']);
        if(count($mediaImages) > 0){
            $this->view->assign('mediaImages', $mediaImages);
        }



        //$image = $images[0] ?? null;


        $this->view->assign('exceptions', $exceptions);




        // $allVars = $renderingContext->getVariableProvider()->getAll();

        // debug($allVars);

        $content = $this->contentObject->data['bodytext'];
        if(empty($content)){
            $content = '<f:debug inline="true">{_all}</f:debug>';
        }
        $view = static::getClonedView($renderingContext);
        $view->setTemplateSource($content);
        $renderedContent = static::renderView($view);


        $this->view->assign('renderedContent', $renderedContent);

        return $this->htmlResponse();
    }

    protected function assignDefaultViewVars()
    {
        if (isset($this->settings['addglobalvariables'])) {
            $addglobalvariables = explode(',', $this->settings['addglobalvariables']);
        } else {
            // $addglobalvariables = ['TSFE','TSSETUP','PAGE','CONTENTOBJECT','REQUEST'];
            $addglobalvariables = array();
        }
        if (in_array('contentobject', $addglobalvariables) && $this->contentObject instanceof ContentObjectRenderer) {
            $this->view->assign('CONTENTOBJECT', $this->contentObject->data);
        }
        if (in_array('tsfe', $addglobalvariables)) {
            /** @var TypoScriptFrontendController $frontendController */
            $frontendController = $this->request->getAttribute('frontend.controller');
            $this->view->assign('TSFE', $frontendController);
        }
        if (in_array('tssetup', $addglobalvariables)) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptSettings = $typoScriptService->convertTypoScriptArrayToPlainArray($GLOBALS['TSFE']->tmpl->setup);
            $this->view->assign('TSSETUP', $typoScriptSettings);
        }
        if (in_array('page', $addglobalvariables)) {
            $pageRepository = $typoScriptService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Domain\Repository\PageRepository::class);
            $page = $pageRepository->getPage($this->contentObject->data['pid']);
            $this->view->assign('PAGE', $page);
        }
        if (in_array('request', $addglobalvariables)) {
            $this->view->assign('REQUEST', $this->request->getQueryParams());
        }
    }

    protected static function getClonedView(RenderingContextInterface $renderingContext): StandaloneView
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $newRenderingContext = $view->getRenderingContext();
        if (method_exists($renderingContext, 'getControllerContext')) {
            $controllerContext = clone $renderingContext->getControllerContext();

            $view->setFormat($controllerContext->getRequest()->getFormat());
            $newRenderingContext->setViewHelperVariableContainer(
                $renderingContext->getViewHelperVariableContainer()
            );
            if (method_exists($newRenderingContext, 'setControllerContext')) {
                $newRenderingContext->setControllerContext($controllerContext);
            }
        } elseif (method_exists($renderingContext, 'getRequest') && method_exists($newRenderingContext, 'setRequest')) {
            $newRenderingContext->setRequest($renderingContext->getRequest());
        }
        $variables = (array)$renderingContext->getVariableProvider()->getAll();
        $view->assignMultiple($variables);
        return $view;
    }


    /**
     * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface|ViewInterface $view
     */
    protected static function renderView($view): string
    {
        try {
            $content = $view->render();
        } catch (\Exception $error) {
            $content = $error->getMessage() . ' (' . $error->getCode() . ')';
        }
        return $content;
    }


}