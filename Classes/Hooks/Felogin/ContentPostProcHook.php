<?php

namespace GeorgRinger\GoogleSignin\Hooks\Felogin;

use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use GeorgRinger\GoogleSignin\Service\StatusService;
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
        $content = $params['content'];
        if (!$this->extensionConfiguration->getEnableFE()) {
            $content = str_replace(['###GOOGLE_SIGNIN_SWITCH_LINK###', '###GOOGLE_SIGNIN_CONTENT###'], '', $content);
            return $content;
        }

        $this->setMetaTag();

        $additionalContent = $this->getView()->render();

        $searchReplace = [
            '###GOOGLE_SIGNIN_SWITCH_LINK###' => '<label for="google-signin">
        <input type="checkbox" name="google-signin" id="google-signin" value="1"/>
            Use google signin
        </label>',
            '###GOOGLE_SIGNIN_CONTENT###' => $additionalContent
        ];

        $content = str_replace(array_keys($searchReplace), array_values($searchReplace), $content);
        return $content;
    }

    protected function setMetaTag()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addHeaderData(
            '<meta name="google-signin-client_id" content="' . $this->extensionConfiguration->getClientId() . '">'
        );
    }

    /**
     * @return StandaloneView
     */
    protected function getView(): StandaloneView
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        try {
            StatusService::isEnabled('FE');
        } catch (\GeorgRinger\GoogleSignin\Error\ConfigurationException $e) {
            $view->assignMultiple([
                'error' => $e
            ]);
        }
        $view->getRequest()->setControllerExtensionName('google_login');
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:google_signin/Resources/Private/Templates/Frontend.html'));
        return $view;
    }
}
