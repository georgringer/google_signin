<?php

namespace GeorgRinger\GoogleSignin\Hooks\Felogin;

use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Felogin\Controller\FrontendLoginController;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ContentPostProcHook
{

    /** @var ExtensionConfiguration */
    protected $extensionConfiguration;

    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    public function run(&$params, FrontendLoginController $frontendLoginController)
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addHeaderData(
            '<meta name="google-signin-client_id" content="' . $this->extensionConfiguration->getClientId() . '">'
        );
        $content = $params['content'];

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->getRequest()->setControllerExtensionName('google_login');
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:google_signin/Resources/Private/Templates/Frontend.html'));

        $additionalContent = $view->render();

        $searchReplace = [
            '###GOOGLE_SIGNIN_SWITCH_LINK###' => '<a href="javascript:loadSrc();">try google login</a>',
            '###GOOGLE_SIGNIN_CONTENT###' => $additionalContent
        ];


        $content = str_replace(array_keys($searchReplace), array_values($searchReplace), $content);
        return $content;
    }
}