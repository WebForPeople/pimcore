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

namespace Pimcore\Bundle\CustomReportsBundle\EventListener;

use Pimcore\Bundle\AdminBundle\Event\IndexActionSettingsEvent;
use Pimcore\Bundle\CustomReportsBundle\Tool\Config;

class IndexSettingsListener
{
    public function indexSettings(IndexActionSettingsEvent $settingsEvent): void
    {
        $settingsEvent->addSetting('custom-reports-writeable', (new Config())->isWriteable());
    }
}
