<?php
namespace GeorgRinger\GoogleSignin\UserProvider;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use TYPO3\CMS\Saltedpasswords\Utility\SaltedPasswordsUtility;

abstract class AbstractUserProvider implements UserProviderInterface
{
    /** @var array */
    protected $authenticationInformation;

    /** @var string  */
    protected $emailFieldName = 'email';

    /**
     * AbstractUserProvider constructor.
     *
     * @param array $authenticationInformation
     */
    public function __construct(array $authenticationInformation)
    {
        $this->authenticationInformation = $authenticationInformation;
    }

    /**
     * @param int $uid
     * @param bool $respectEnableFields
     * @return array
     */
    public function getUserById(int $uid, bool $respectEnableFields = true): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');
        $queryBuilder->getRestrictions()->removeAll();

        $records = $queryBuilder
            ->select('*')
            ->from($this->authenticationInformation['db_user']['table'])
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($uid, \PDO::PARAM_INT)
                ),
                $this->authenticationInformation['db_user']['check_pid_clause'],
                $this->authenticationInformation['db_user']['enable_clause']
            )
            ->execute()
            ->fetchAll();

        if (count($records) === 1) {
            return $records[0];
        }

        return [];
    }

    /**
     * @param string $email
     * @param bool $respectEnableFields
     * @return array
     * @throws \UnexpectedValueException
     */
    public function getUserByEmail(string $email, bool $respectEnableFields = true): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->authenticationInformation['db_user']['table']);

        if (!$respectEnableFields) {
            $queryBuilder
                ->getRestrictions()
                ->removeAll()
                ->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        }

        $records = $queryBuilder
            ->select('*')
            ->from($this->authenticationInformation['db_user']['table'])
            ->where(
                $queryBuilder->expr()->eq(
                    $this->emailFieldName,
                    $queryBuilder->createNamedParameter($email, \PDO::PARAM_STR)
                ),
                $this->authenticationInformation['db_user']['check_pid_clause'],
                $this->authenticationInformation['db_user']['enable_clause']
            )
            ->execute()
            ->fetchAll();

        $count = count($records);
        if ($count > 1) {
            throw new \UnexpectedValueException(sprintf('Too many records found for email address "%s".', $email), 1527920312);
        }
        if ($count === 1) {
            return $records[0];
        }

        return [];
    }

    /**
     * Generates a random password.
     *
     * @return string
     */
    protected function generatePassword(): string
    {
        $password = StringUtility::getUniqueId('google_auth_password_');

        if (SaltedPasswordsUtility::isUsageEnabled()) {
            $objInstanceSaltedPW = SaltFactory::getSaltingInstance();
            $password = $objInstanceSaltedPW->getHashedPassword($password);
        }

        return $password;
    }
}