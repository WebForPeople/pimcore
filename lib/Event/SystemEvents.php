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

final class SystemEvents
{
    /**
     * This event is fired on shutdown (register_shutdown_function)
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const SHUTDOWN = 'pimcore.system.shutdown';

    /**
     * 	See Console / CLI | allow to register console commands (e.g. through plugins)
     *
     * @Event("Pimcore\Event\System\ConsoleEvent")
     *
     * @var string
     */
    const CONSOLE_INIT = 'pimcore.system.console.init';

    /**
     * This event is fired on maintenance mode activation
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MAINTENANCE_MODE_ACTIVATE = 'pimcore.system.maintenance_mode.activate';

    /**
     * This event is fired on maintenance mode deactivation
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MAINTENANCE_MODE_DEACTIVATE = 'pimcore.system.maintenance_mode.deactivate';

    /**
     * This event is fired when maintenance mode is scheduled for the next login
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MAINTENANCE_MODE_SCHEDULE_LOGIN = 'pimcore.system.maintenance_mode.schedule_login';

    /**
     * This event is fired when maintenance mode is unscheduled
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const MAINTENANCE_MODE_UNSCHEDULE_LOGIN = 'pimcore.system.maintenance_mode.unschedule_login';

    /**
     * This event is fired on Full-Page Cache clear
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const CACHE_CLEAR_FULLPAGE_CACHE = 'pimcore.system.cache.clearFullpageCache';

    /**
     * This event is fired on Cache clear
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const CACHE_CLEAR = 'pimcore.system.cache.clear';

    /**
     * This event is fired on Temporary Files clear
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const CACHE_CLEAR_TEMPORARY_FILES = 'pimcore.system.cache.clearTemporaryFiles';

    /**
     * This event is fired before Pimcore adjusts element keys to generic rules
     *
     * @Event("\Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const SERVICE_PRE_GET_VALID_KEY = 'pimcore.system.service.preGetValidKey';

    /**
     * This event is fired before element service returns deep copy instance
     *
     * Arguments:
     *  - copier | deep copy instance
     *  - element | source element for deep copy
     *  - context | context info array i.e. 'source' => calling method, 'conversion' => 'marshal'/'unmarshal', 'defaultFilter' => true/false
     *
     * @Event("\Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const SERVICE_PRE_GET_DEEP_COPY = 'pimcore.system.service.preGetDeepCopy';

    /**
     * The SAVE_SYSTEM_SETTINGS event is triggered when the system settings are saved.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const SAVE_ACTION_SYSTEM_SETTINGS = 'pimcore.system.settings.saveAction';

    /**
     * The GET_SYSTEM_CONFIGURATION event is triggered when the system configuration is requested.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const GET_SYSTEM_CONFIGURATION = 'pimcore.system.configuration.get';
}
