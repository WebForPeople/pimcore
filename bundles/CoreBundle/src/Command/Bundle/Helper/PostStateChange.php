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

namespace Pimcore\Bundle\CoreBundle\Command\Bundle\Helper;

use Pimcore\Cache\Symfony\CacheClearer;
use Pimcore\Console\Style\PimcoreStyle;
use Pimcore\Tool\AssetsInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * @internal
 */
class PostStateChange
{
    public function __construct(
        private CacheClearer $cacheClearer,
        private AssetsInstaller $assetsInstaller,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    public static function configureStateChangeCommandOptions(Command $command): void
    {
        $command->addOption(
            'no-post-change-commands',
            null,
            InputOption::VALUE_NONE,
            'Do not run any post change commands (<comment>assets:install</comment>, <comment>cache:clear</comment>) after successful state change'
        );

        $command->addOption(
            'no-assets-install',
            null,
            InputOption::VALUE_NONE,
            'Do not run <comment>assets:install</comment> command after successful state change'
        );

        $command->addOption(
            'no-cache-clear',
            null,
            InputOption::VALUE_NONE,
            'Do not run <comment>cache:clear</comment> command after successful state change'
        );
    }

    public function runPostStateChangeCommands(PimcoreStyle $io, string $environment): void
    {
        $input = $io->getInput();

        if ($input->getOption('no-post-change-commands')) {
            return;
        }

        $runAssetsInstall = $input->getOption('no-assets-install') ? false : true;
        $runCacheClear = $input->getOption('no-cache-clear') ? false : true;

        if (!$runAssetsInstall && !$runCacheClear) {
            return;
        }

        $runCallback = function ($type, $buffer) use ($io) {
            $io->write($buffer);
        };

        $io->newLine();
        $io->section('Running post state change commands');

        if ($runAssetsInstall) {
            $io->comment('Running bin/console assets:install...');

            try {
                $this->assetsInstaller->setRunCallback($runCallback);
                $this->assetsInstaller->install([
                    'env' => $environment,
                    'ansi' => $io->isDecorated(),
                ]);
            } catch (ProcessFailedException $e) {
                // noop - output should be enough
            }
        }

        if ($runCacheClear) {
            // remove terminate event listeners as they break with a cleared container
            foreach ($this->eventDispatcher->getListeners(ConsoleEvents::TERMINATE) as $listener) {
                $this->eventDispatcher->removeListener(ConsoleEvents::TERMINATE, $listener);
            }

            $io->comment('Running bin/console cache:clear...');

            try {
                $this->cacheClearer->setRunCallback($runCallback);
                $this->cacheClearer->clear($environment, [
                    'ansi' => $io->isDecorated(),
                ]);
            } catch (ProcessFailedException $e) {
                // noop - output should be enough
            }
        }
    }
}
