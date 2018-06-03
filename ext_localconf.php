<?php
defined('TYPO3_MODE') || die();

$extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration::class);

$subTypes = [];
if ($extensionConfiguration->getEnableFE()) {
    $subTypes[] = 'getUserFE,authUserFE';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['postProcContent']['google_signin'] =
        \GeorgRinger\GoogleSignin\Hooks\Felogin\ContentPostProcHook::class . '->run';
}
if ($extensionConfiguration->getEnableBE()) {
    $subTypes[] = 'getUserBE,authUserBE';

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1526966634] = [
        'provider' => \GeorgRinger\GoogleSignin\LoginProvider\GoogleSignInProvider::class,
        'sorting' => 25,
        'icon-class' => 'fa-google',
        'label' => 'LLL:EXT:google_signin/Resources/Private/Language/locallang.xlf:backendLogin.switch.label'
    ];
}

if (!empty($subTypes)) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService(
        'google_signin',
        'auth',
        'tx_googlelogin_service',
        [
            'title' => 'Google Login Authentication',
            'description' => 'Google Login service for Frontend and Backend',
            'subtype' => implode(',', $subTypes),
            'available' => true,
            'priority' => 75,
            // Must be higher than for \TYPO3\CMS\Sv\AuthenticationService (50) or \TYPO3\CMS\Sv\AuthenticationService will log failed login attempts
            'quality' => 50,
            'os' => '',
            'exec' => '',
            'className' => \GeorgRinger\GoogleSignin\Service\GoogleLoginService::class
        ]
    );
}

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$icons = [
    'ext-googlelogin-logo-google' => 'google_logo.svg',
];
foreach ($icons as $identifier => $path) {
    $iconRegistry->registerIcon(
        $identifier,
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:google_signin/Resources/Public/Icons/' . $path]
    );
}
