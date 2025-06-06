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

class BundleManagerEvents
{
    /**
     * The CSS_PATHS event is triggered for paths to CSS files which are about to be loaded for the admin interface.
     *
     * @Event("Pimcore\Event\BundleManager\PathsEvent")
     *
     * @var string
     */
    const CSS_PATHS = 'pimcore.bundle_manager.paths.css';

    /**
     * The JS_PATHS event is triggered for paths to JS files which are about to be loaded for the admin interface.
     *
     * @Event("Pimcore\Event\BundleManager\PathsEvent")
     *
     * @var string
     */
    const JS_PATHS = 'pimcore.bundle_manager.paths.js';

    /**
     * The EDITMODE_CSS_PATHS event is triggered for paths to CSS files which are about to be loaded in editmode.
     *
     * @Event("Pimcore\Event\BundleManager\PathsEvent")
     *
     * @var string
     */
    const EDITMODE_CSS_PATHS = 'pimcore.bundle_manager.paths.editmode_css';

    /**
     * The EDITMODE_JS_PATHS event is triggered for paths to JS files which are about to be loaded in editmode.
     *
     * @Event("Pimcore\Event\BundleManager\PathsEvent")
     *
     * @var string
     */
    const EDITMODE_JS_PATHS = 'pimcore.bundle_manager.paths.editmode_js';
}
