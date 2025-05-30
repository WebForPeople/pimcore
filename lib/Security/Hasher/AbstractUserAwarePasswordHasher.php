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

namespace Pimcore\Security\Hasher;

use Symfony\Component\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Symfony\Component\Security\Core\Exception\RuntimeException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @internal
 */
abstract class AbstractUserAwarePasswordHasher extends PlaintextPasswordHasher implements UserAwarePasswordHasherInterface
{
    protected ?UserInterface $user = null;

    public function setUser(UserInterface $user): void
    {
        if ($this->user) {
            throw new RuntimeException('User was already set and can\'t be overwritten');
        }

        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        if (!$this->user) {
            throw new RuntimeException('No user was set');
        }

        return $this->user;
    }
}
