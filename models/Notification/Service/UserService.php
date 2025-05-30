<?php

declare(strict_types=1);

/**
 * This source file is available under the terms of the
 * Pimcore Open Core License (POCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (https://www.pimcore.com)
 *  @license    Pimcore Open Core License (POCL)
 */

namespace Pimcore\Model\Notification\Service;

use Pimcore\Model\User;

/**
 * @internal
 */
class UserService
{
    public function findAll(User $loggedIn): array
    {
        // condition for users with groups having notifications permission
        $condition = [];
        $rolesList = new \Pimcore\Model\User\Role\Listing();
        $rolesList->addConditionParam("CONCAT(',', permissions, ',') LIKE ?", '%,notifications,%');
        $rolesList->load();
        $roles = $rolesList->getRoles();

        foreach ($roles as $role) {
            $condition[] = "CONCAT(',', roles, ',') LIKE '%," . $role->getId() . ",%'";
        }

        // get available users having notifications permission or having a group with notifications permission
        $userListing = new User\Listing();
        $userListing->setOrderKey('name');
        $userListing->setOrder('ASC');

        $condition[] = 'admin = 1';
        $userListing->addConditionParam("((CONCAT(',', permissions, ',') LIKE ? ) OR " . implode(' OR ', $condition) . ')', '%,notifications,%');
        $userListing->addConditionParam('id != ?', $loggedIn->getId());
        $userListing->addConditionParam('active = ?', '1');
        $userListing->load();
        $users = $userListing->getUsers();

        return array_merge($users, $roles);
    }

    public function filterUsersWithPermission(array $users): array
    {
        $usersList = [];

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->isAllowed('notifications')) {
                $usersList[] = $user;
            }
        }

        return $usersList;
    }
}
