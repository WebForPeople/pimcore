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

namespace Pimcore\Tests\Support\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Lib\ModuleContainer;
use Pimcore\Bundle\GlossaryBundle\Installer;
use Pimcore\Bundle\GlossaryBundle\Model\Glossary;
use Pimcore\Tests\Support\Util\Autoloader;

class Unit extends \Codeception\Module
{
    public function __construct(ModuleContainer $moduleContainer, ?array $config = null)
    {
        $this->config = array_merge($this->config, [
            'run_installer' => true,
        ]);

        parent::__construct($moduleContainer, $config);
    }

    public function _beforeSuite(array $settings = []): void
    {
        $this->installPimcoreGlossaryBundle();
    }

    private function installPimcoreGlossaryBundle(): void
    {
        if ($this->config['run_installer']) {
            /** @var Pimcore $pimcoreModule */
            $pimcoreModule = $this->getModule('\\' . Pimcore::class);

            $this->debug('[PimcoreGlossaryBundle] Running PimcoreGlossaryBundle installer');

            // install ecommerce framework
            $installer = $pimcoreModule->getContainer()->get(Installer::class);
            $installer->install();

            //explicitly load installed classes so that the new ones are used during tests
            Autoloader::load(Glossary::class);
        }
    }
}
