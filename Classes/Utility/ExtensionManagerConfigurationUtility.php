<?php

namespace GeorgRinger\GoogleSignin\Utility;

/**
 * This file is part of the "google_signin" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Create custom fields for extension configuration
 */
class ExtensionManagerConfigurationUtility
{

    public function getUserTemplate(array $params): string
    {
        $userRows = $this->getBeUsers((int)$params['fieldValue']);

        $html = '<select class="form-control" name="' . $params['fieldName'] . '"><option value="0"></option>';
        if ($userRows[1]) {
            $html .= '<optgroup label="Admins">' . implode(LF, $userRows[1]);
        }
        if ($userRows[0]) {
            $html .= '<optgroup label="Editors">' . implode(LF, $userRows[0]);
        }

        return $html;
    }

    /**
     * Get all BE users, split by if admin or not
     *
     * @param int $selected selected id
     * @return array
     */
    protected function getBeUsers(int $selected): array
    {
        $rows = [];
        $qb = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');

        $tempRows = $qb
            ->select('username', 'uid', 'realName', 'admin')
            ->from('be_users')
            ->orderBy('username')
            ->execute()
            ->fetchAll();

        foreach ($tempRows as $row) {
            $isSelected = (int)$row['uid'] === $selected ? ' selected="selected" ' : '';
            $title = htmlspecialchars($row['username']);
            if ($row['realName']) {
                $title .= ' (' . htmlspecialchars($row['realName']) . ')';
            }
            $rows[(int)$row['admin']][$row['uid']] = '<option ' . $isSelected . 'value="' . $row['uid'] . '">' . $title . '</option>';
        }

        return $rows;
    }
}