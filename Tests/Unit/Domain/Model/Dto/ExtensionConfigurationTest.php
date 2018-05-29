<?php

namespace GeorgRinger\GoogleSignin\Tests\Unit\Domain\Model\Dto;

/**
 * This file is part of the "google_signin" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ExtensionConfigurationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function clientIdCanBeRead()
    {
        $value = '123xy';
        $settings = ['clientId' => $value];
        $this->setSettings($settings);
        $extensionConfiguration = new ExtensionConfiguration();
        $this->assertEquals($value, $extensionConfiguration->getClientId());
    }

    /**
     * @test
     */
    public function enabledFeCanBeRead()
    {
        $value = true;
        $settings = ['enableFE' => $value];
        $this->setSettings($settings);
        $extensionConfiguration = new ExtensionConfiguration();
        $this->assertEquals($value, $extensionConfiguration->getEnableFE());
    }

    /**
     * @test
     */
    public function enabledBeCanBeRead()
    {
        $value = true;
        $settings = ['enableBE' => $value];
        $this->setSettings($settings);
        $extensionConfiguration = new ExtensionConfiguration();
        $this->assertEquals($value, $extensionConfiguration->getEnableBE());
    }

    protected function setSettings(array $settings)
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['google_signin'] = serialize($settings);
    }
}
