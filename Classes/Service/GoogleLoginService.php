<?php

namespace GeorgRinger\GoogleSignin\Service;

use GeorgRinger\GoogleSignin\Domain\Model\Dto\ExtensionConfiguration;
use GeorgRinger\GoogleSignin\UserProvider\BackendUserProvider;
use GeorgRinger\GoogleSignin\UserProvider\FrontendUserProvider;
use GeorgRinger\GoogleSignin\UserProvider\UserProviderInterface;
use TYPO3\CMS\Core\Authentication\AbstractUserAuthentication;
use TYPO3\CMS\Core\Service\AbstractService;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use GeorgRinger\GoogleSignin\Error\ConfigurationException;

class GoogleLoginService extends AbstractService
{

    protected $extKey = 'google_signin';

    /**
     * google response
     */
    protected $googleResponse = [];

    /**
     * Additional authentication information provided by AbstractUserAuthentication.
     * We use it to decide what database table contains user records.
     *
     * @var array
     */
    protected $authenticationInformation = [];

    /** @var TimeTracker */
    protected $timeTracker;

    /** @var UserProviderInterface */
    protected $userProvider;

    public function init()
    {
        return true;
    }

    /**
     * Initializes authentication for this service.
     *
     * @param string $subType : Subtype for authentication (either "getUserFE" or "getUserBE")
     * @param array $loginData : Login data submitted by user and preprocessed by AbstractUserAuthentication
     * @param array $authenticationInformation : Additional TYPO3 information for authentication services (unused here)
     * @param AbstractUserAuthentication $parentObject Calling object
     * @return void
     */
    public function initAuth($subType, array $loginData, array $authenticationInformation, AbstractUserAuthentication $parentObject)
    {
        // Store login and authentication data
        $this->loginData = $loginData;
        $this->authenticationInformation = $authenticationInformation;

        $postVars = GeneralUtility::_POST();
        if (isset($postVars['google-signin']) && (int)$postVars['google-signin'] === 1 && isset($postVars['google-token'])) {
            $token = $postVars['google-token'];
            if ($token) {
                $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
//                $client = new Google_Client(['client_id' => $extensionConfiguration->getClientId()]);  // Specify the CLIENT_ID of the app that accesses the backend
//                $payload = $client->verifyIdToken($token);
                $url = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token;
                $result = GeneralUtility::getUrl($url);
                if ($result) {
                    $this->googleResponse = json_decode($result, true);
                }
            }
        }

        $this->parentObject = $parentObject;
    }

    /**
     * Process the submitted OpenID URL if valid.
     *
     * @param array $loginData Credentials that are submitted and potentially modified by other services
     * @param string $passwordTransmissionStrategy Keyword of how the password has been hashed or encrypted before submission
     * @return bool
     */
    public function processLoginData(array &$loginData, $passwordTransmissionStrategy)
    {
        $isProcessed = false;
        // Pre-process the login only if no password has been submitted
        if (empty($loginData['uident_text'])) {
            try {
                $isProcessed = true;
            } catch (\Exception $e) {
                $this->writeLog($e->getMessage());
            }
        }
        return $isProcessed;
    }

    /**
     * This function returns the user record back to the AbstractUserAuthentication.
     * It does not mean that user is authenticated, it means only that user is found. This
     * function makes sure that user cannot be authenticated by any other service
     * if user tries to use OpenID to authenticate.
     *
     * @return mixed User record (content of fe_users/be_users as appropriate for the current mode)
     */
    public function getUser()
    {
        if ($this->loginData['status'] !== 'login') {
            return null;
        }

        if (empty($this->googleResponse)) {
            return null;
        }

        if ($this->authenticationInformation['loginType'] === 'BE') {
            $this->userProvider = GeneralUtility::makeInstance(BackendUserProvider::class, $this->authenticationInformation);
        } elseif ($this->authenticationInformation['loginType'] === 'FE') {
            $this->userProvider = GeneralUtility::makeInstance(FrontendUserProvider::class, $this->authenticationInformation);
        } else {
            $this->writeLog('Invalid loginType \'%s\' given. Only BE and FE is supported', $this->authenticationInformation['loginType']);
            return null;
        }

        $userRecord = $this->userProvider->getUserByEmail($this->googleResponse['email']);

        if (!empty($userRecord) && is_array($userRecord)) {
            $this->writeLog('User \'%s\' logged in with google login \'%s\'', $userRecord[$this->parentObject->formfield_uname], $this->googleResponse['email']);
        } else {
            $isUserInOrganisation = $this->userProvider->isUserInApprovedOrganisation($this->googleResponse['hd']);
            $userDoesNotExist = empty($this->userProvider->getUserByEmail($this->googleResponse['email'], false));

            if ($isUserInOrganisation && $userDoesNotExist) {
                try {
                    $this->userProvider->copyUserFromSkeleton($this->googleResponse['email'], $this->googleResponse['name']);
                    $userRecord = $this->getUser();
                } catch (ConfigurationException $e) {
                    $this->writeLog('Failed to create user for organisation \'%s\' using google login \'%s\'', $this->googleResponse['hd'], $this->googleResponse['email']);
                }
            } else {
                $this->writeLog('Failed to login user using google login \'%s\'', $this->googleResponse['email']);
            }
        }

        return $userRecord;
    }

    /**
     * Authenticates user
     *
     * @param array $userRecord User record
     * @return int Code that shows if user is really authenticated.
     */
    public function authUser(array $userRecord): int
    {
        $result = 100;
        // 100 means "we do not know, continue"
        if (!empty($this->googleResponse)) {
            if ($this->googleResponse['email'] === $userRecord['email']) {
                $result = 200;
            } else {
                $this->writeLog('google login authentication failed2');
            }
        } else {
            $this->writeLog('google login authentication failed');
        }
        return $result;
    }


    /**
     * Writes log message. Destination log depends on the current system mode.
     * For FE the function writes to the admin panel log. For BE messages are
     * sent to the system log. If developer log is enabled, messages are also
     * sent there.
     *
     * This function accepts variable number of arguments and can format
     * parameters. The syntax is the same as for sprintf()
     *
     * @param string $message Message to output
     * @return void
     * @see GeneralUtility::sysLog()
     * @see \TYPO3\CMS\Core\TimeTracker\TimeTracker::setTSlogMessage()
     */
    protected function writeLog($message)
    {
        if (func_num_args() > 1) {
            $params = func_get_args();
            array_shift($params);
            $message = vsprintf($message, $params);
        }
        if (TYPO3_MODE === 'BE') {
            GeneralUtility::sysLog($message, $this->extKey, GeneralUtility::SYSLOG_SEVERITY_NOTICE);
        } else {
            if (is_null($this->timeTracker)) {
                $this->timeTracker = GeneralUtility::makeInstance(TimeTracker::class);
            }
            $this->timeTracker->setTSlogMessage($message);
        }
    }
}