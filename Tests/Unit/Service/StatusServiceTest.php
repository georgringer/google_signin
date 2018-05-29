<?php

namespace GeorgRinger\GoogleSignin\Tests\Unit\Domain\Model\Dto;

/**
 * This file is part of the "google_signin" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GeorgRinger\GoogleSignin\Service\StatusService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UnexpectedValueException;

class StatusServiceTest extends UnitTestCase
{
    
    /**
     * @test
     * @expectedException UnexpectedValueException
     */
    public function expectExceptionForInvalidMode()
    {
        StatusService::isEnabled('XYZ');
    }

    /**
     * @test
     * @expectedException \GeorgRinger\GoogleSignin\Error\ConfigurationException
     * @expectedExceptionCode 1527560544
     */
    public function expectExceptionForNonClient()
    {
        $_SERVER['SSL_SESSION_ID'] = 1;
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxySSL'] = '*';
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'] = GeneralUtility::getIndpEnv('REMOTE_ADDR');


        StatusService::isEnabled('FE');
    }

}