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
    protected $beAutomaticUserCreationForOrganization = '';

    /** @var int */
    protected $beAutomaticUserCreationTemplate = 0;

    /** @var string */
    protected $feAutomaticUserCreationForOrganization = '';

    /** @var int */
    protected $feAutomaticUserCreationTemplate = 0;

    public function __construct()
    {
        $settings = (array)unserialize((string)$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['google_signin'], ['allowed_classes' => false]);
        if (!empty($settings)) {
            $this->clientId = (string)$settings['clientId'];
            $this->enableBE = (bool)$settings['enableBE'];
            $this->enableFE = (bool)$settings['enableFE'];
            $this->beAutomaticUserCreationForOrganization = (string)$settings['beAutomaticUserCreationForOrganization'];
            $this->beAutomaticUserCreationTemplate = (int)$settings['beAutomaticUserCreationTemplate'];
            $this->feAutomaticUserCreationForOrganization = (string)$settings['feAutomaticUserCreationForOrganization'];
            $this->feAutomaticUserCreationTemplate = (int)$settings['feAutomaticUserCreationTemplate'];
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
    public function getBeAutomaticUserCreationForOrganization(): string
    {
        return $this->beAutomaticUserCreationForOrganization;
    }

    /**
     * @return int
     */
    public function getBeAutomaticUserCreationTemplate(): int
    {
        return $this->beAutomaticUserCreationTemplate;
    }

    /**
     * @return string
     */
    public function getFeAutomaticUserCreationForOrganization(): string
    {
        return $this->feAutomaticUserCreationForOrganization;
    }

    /**
     * @return int
     */
    public function getFeAutomaticUserCreationTemplate(): int
    {
        return $this->feAutomaticUserCreationTemplate;
    }
}
