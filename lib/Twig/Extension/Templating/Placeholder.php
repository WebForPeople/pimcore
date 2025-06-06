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

/**
 * ----------------------------------------------------------------------------------
 * based on @author ZF1 Zend_View_Helper_Placeholder
 * ----------------------------------------------------------------------------------
 */

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Pimcore\Twig\Extension\Templating;

use Pimcore\Twig\Extension\Templating\Placeholder\AbstractExtension;
use Pimcore\Twig\Extension\Templating\Placeholder\Container;

/**
 * Helper for passing data between otherwise segregated Views. It's called
 * Placeholder to make its typical usage obvious, but can be used just as easily
 * for non-Placeholder things. That said, the support for this is only
 * guaranteed to effect subsequently rendered templates, and of course Layouts.
 *
 */
class Placeholder extends AbstractExtension
{
    /**
     * Registry key under which container registers itself
     *
     */
    protected string $_regKey = 'Placeholder';

    /**
     * @var Container[]
     */
    protected array $containers = [];

    /**
     * Retrieve object instance; optionally add meta tag
     *
     *
     */
    public function __invoke(?string $containerName = null): Container
    {
        $containerName = (string) $containerName;
        if (empty($this->containers[$containerName])) {
            $this->containers[$containerName] = $this->containerService->getContainer($this->_regKey . '_' . $containerName);
        }

        return $this->containers[$containerName];
    }
}
