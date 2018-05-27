<?php
declare(strict_types=1);

namespace GeorgRinger\GoogleSignin\Domain\Model\Dto;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ExtensionConfiguration implements SingletonInterface
{

    /** @var string */
    protected $clientId = '';

    /** @var bool */
    protected $enableFE = false;

    /** @var bool */
    protected $enableBE = false;

    /** @var string */
    protected $automaticUserCreationForOrganization = '';

    public function __construct()
    {
        $settings = (array)unserialize((string)$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['google_signin'], ['allowed_classes' => false]);
        if (!empty($settings)) {
            $this->clientId = $settings['clientId'];
            $this->enableBE = (bool)$settings['enableBE'];
            $this->enableFE = (bool)$settings['enableFE'];
            $this->automaticUserCreationForOrganization = (string)$settings['automaticUserCreationForOrganization'];
        }
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return bool
     */
    public function getEnableFE(): bool
    {
        return $this->enableFE;
    }

    /**
     * @return bool
     */
    public function getEnableBE(): bool
    {
        return $this->enableBE;
    }

    /**
     * @return string
     */
    public function getAutomaticUserCreationForOrganization(): string
    {
        return $this->automaticUserCreationForOrganization;
    }
}
