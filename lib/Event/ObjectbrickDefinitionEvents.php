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

namespace Pimcore\Event;

final class ObjectbrickDefinitionEvents
{
    /**
     * @Event("Pimcore\Event\Model\DataObject\ObjectbrickDefinitionEvent")
     *
     * @var string
     */
    const PRE_ADD = 'pimcore.objectbrick.preAdd';

    /**
     * @Event("Pimcore\Event\Model\DataObject\ObjectbrickDefinitionEvent")
     *
     * @var string
     */
    const POST_ADD = 'pimcore.objectbrick.postAdd';

    /**
     * @Event("Pimcore\Event\Model\DataObject\ObjectbrickDefinitionEvent")
     *
     * @var string
     */
    const PRE_UPDATE = 'pimcore.objectbrick.preUpdate';

    /**
     * @Event("Pimcore\Event\Model\DataObject\ObjectbrickDefinitionEvent")
     *
     * @var string
     */
    const POST_UPDATE = 'pimcore.objectbrick.postUpdate';

    /**
     * @Event("Pimcore\Event\Model\DataObject\ObjectbrickDefinitionEvent")
     *
     * @var string
     */
    const PRE_DELETE = 'pimcore.objectbrick.preDelete';

    /**
     * @Event("Pimcore\Event\Model\DataObject\ObjectbrickDefinitionEvent")
     *
     * @var string
     */
    const POST_DELETE = 'pimcore.objectbrick.postDelete';
}
