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

namespace Pimcore\Security\User;

use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @internal
 *
 * User provider loading users from pimcore objects. To load users, the provider needs
 * to know which kind of users to load (className) and which field to query for the
 * username (usernameField).
 *
 * Example DI configuration loading from the App\Model\DataObject\User class and searching by username:
 *
 *      website_demo.security.user_provider:
 *          class: Pimcore\Security\User\ObjectUserProvider
 *          arguments: ['App\Model\DataObject\User', 'username']
 */
class ObjectUserProvider implements UserProviderInterface
{
    /**
     * The pimcore class name to be used. Needs to be a fully qualified class
     * name (e.g. Pimcore\Model\DataObject\User or your custom user class extending
     * the generated one.
     *
     */
    protected string $className;

    protected string $usernameField = 'username';

    public function __construct(string $className, string $usernameField = 'username')
    {
        $this->setClassName($className);

        $this->usernameField = $usernameField;
    }

    protected function setClassName(string $className): void
    {
        if ($className === '') {
            throw new InvalidArgumentException('Object class name is empty');
        }

        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('User class %s does not exist', $className));
        }

        if (!is_subclass_of($className, AbstractObject::class)) {
            throw new InvalidArgumentException(sprintf('User class %s must be a subclass of %s', $className, AbstractObject::class));
        }

        $this->className = $className;
    }

    public function loadUserByIdentifier(string $username): UserInterface
    {
        $getter = sprintf('getBy%s', ucfirst($this->usernameField));

        // User::getByUsername($username, 1);
        $user = call_user_func_array([$this->className, $getter], [$username, 1]);
        if ($user && $user instanceof $this->className) {
            return $user;
        }

        throw new UserNotFoundException(sprintf('User %s was not found', $username));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof $this->className || !$user instanceof AbstractObject) {
            throw new UnsupportedUserException();
        }

        return $this->className::getById($user->getId());
    }

    public function supportsClass(string $class): bool
    {
        return $class === $this->className;
    }
}
