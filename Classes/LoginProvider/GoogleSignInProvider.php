<?php

namespace GeorgRinger\GoogleSignin\LoginProvider;

use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Backend\LoginProvider\LoginProviderInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class GoogleSignInProvider implements LoginProviderInterface
{

    /** @var ExtensionConfiguration */
    protected $extensionConfiguration;

    public function __construct()
    {
        $this->extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    }

    /**
     * @param StandaloneView $view
     * @param PageRenderer $pageRenderer
     * @param LoginController $loginController
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController)
    {
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:google_signin/Resources/Private/Templates/GoogleSignIn.html'));
        $view->assignMultiple([
            'errorCodes' => $this->getErrorCodes()
        ]);

        $pageRenderer->addHeaderData(
            '<meta name="google-signin-client_id" content="' . $this->extensionConfiguration->getClientId() . '">'
        );
    }

    /**
     * @return array
     */
    protected function getErrorCodes(): array
    {
        $errorCodes = [];
        if (!$this->extensionConfiguration->getClientId()) {
            $errorCodes[] = 'noCLientId';
        }
        if (!GeneralUtility::getIndpEnv('TYPO3_SSL')) {
            $errorCodes[] = 'noTls';
        }

        return $errorCodes;
    }
}
