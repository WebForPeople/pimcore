<?php

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

use Pimcore\Security\User\User;
use Pimcore\Tool\Authentication;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * As pimcore needs the user information when hashing the password, every user gets his own hasher instance with a
 * user object. If user is no pimcore user, fall back to default implementation.
 *
 * @method User getUser()
 *
 * @internal
 */
class PimcoreUserPasswordHasher extends AbstractUserAwarePasswordHasher
{
    use CheckPasswordLengthTrait;

    public function hash(string $plainPassword, ?string $salt = null): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new BadCredentialsException(sprintf('Password exceeds a maximum of %d characters', static::MAX_PASSWORD_LENGTH));
        }

        return Authentication::getPasswordHash($this->getUser()->getUserIdentifier(), $plainPassword);
    }

    public function verify(string $hashedPassword, string $plainPassword, ?string $salt = null): bool
    {
        if ($this->isPasswordTooLong($hashedPassword)) {
            return false;
        }

        return Authentication::verifyPassword($this->getUser()->getUser(), $plainPassword);
    }
}
