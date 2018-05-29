<?php

namespace GeorgRinger\GoogleSignin\Service;

use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use GeorgRinger\GoogleSignin\Error\ConfigurationException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

class StatusService
{

    /**
     * @param string $mode
     * @return bool
     * @throws UnexpectedValueException
     * @throws ConfigurationException
     */
    public static function isEnabled(string $mode): bool
    {
        if (!GeneralUtility::inList('FE,BE', $mode)) {
            throw new UnexpectedValueException(sprintf('Mode "%s" is not supported', $mode), 1527560262);
        }
        if (!GeneralUtility::getIndpEnv('TYPO3_SSL')) {
            throw new ConfigurationException('SSL is required', 1527560543);
        }

        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);

        if (!$extensionConfiguration->getClientId()) {
            throw new ConfigurationException('No client it set', 1527560544);
        }
        if ($mode === 'BE') {
            return $extensionConfiguration->getEnableBE();
        }
        return $extensionConfiguration->getEnableFE();

    }
}