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

namespace Pimcore\Model\DataObject\ClassDefinition\DynamicOptionsProvider;

use Pimcore\Model\DataObject\ClassDefinition\Data;

trigger_deprecation('pimcore/pimcore', '11.2', '%s is deprecated. Use %s instead.', MultiSelectOptionsProviderInterface::class, SelectOptionsProviderInterface::class);

/**
 * @deprecated use SelectOptionsProviderInterface instead
 */
interface MultiSelectOptionsProviderInterface
{
    public function getOptions(array $context, Data $fieldDefinition): array;

    /**
     * Whether options are depending on the object context (i.e. different options for different objects) or not.
     * This is especially important for exposing options in the object grid. For options depending on object-context
     * there will be no batch assignment mode, and filtering can only be done through a text field instead of the
     * options list.
     *
     *
     */
    public function hasStaticOptions(array $context, Data $fieldDefinition): bool;
}
