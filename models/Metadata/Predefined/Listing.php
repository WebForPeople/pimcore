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

namespace Pimcore\Model\Metadata\Predefined;

use Exception;
use Pimcore\Model\AbstractModel;
use Pimcore\Model\Listing\CallableFilterListingInterface;
use Pimcore\Model\Listing\CallableOrderListingInterface;
use Pimcore\Model\Listing\Traits\FilterListingTrait;
use Pimcore\Model\Listing\Traits\OrderListingTrait;

/**
 * @internal
 *
 * @method \Pimcore\Model\Metadata\Predefined\Listing\Dao getDao()
 * @method int getTotalCount()
 */
class Listing extends AbstractModel implements CallableFilterListingInterface, CallableOrderListingInterface
{
    use FilterListingTrait;
    use OrderListingTrait;

    /**
     * @var \Pimcore\Model\Metadata\Predefined[]|null
     */
    protected ?array $definitions = null;

    /**
     * @return \Pimcore\Model\Metadata\Predefined[]
     */
    public function getDefinitions(): array
    {
        if ($this->definitions === null) {
            $this->getDao()->loadList();
        }

        return $this->definitions;
    }

    /**
     * @param \Pimcore\Model\Metadata\Predefined[]|null $definitions
     *
     * @return $this
     */
    public function setDefinitions(?array $definitions): static
    {
        $this->definitions = $definitions;

        return $this;
    }

    /**
     *
     * @return \Pimcore\Model\Metadata\Predefined[]|null
     *
     * @throws Exception
     */
    public static function getByTargetType(string $type, array|string|null $subTypes = null): ?array
    {
        if ($type !== 'asset') {
            throw new Exception('other types than assets are currently not supported');
        }

        $list = new self();

        if ($subTypes && !is_array($subTypes)) {
            $subTypes = [$subTypes];
        }

        if (is_array($subTypes)) {
            return array_filter($list->load(), function ($item) use ($subTypes) {
                if (empty($item->getTargetSubtype())) {
                    return true;
                }

                if (in_array($item->getTargetSubtype(), $subTypes)) {
                    return true;
                }

                return false;
            });
        }

        return $list->load();
    }

    public static function getByKeyAndLanguage(string $key, ?string $language, ?string $targetSubtype = null): ?\Pimcore\Model\Metadata\Predefined
    {
        $list = new self();

        foreach ($list->load() as $item) {
            if ($item->getName() != $key) {
                continue;
            }

            if ($language && $language != $item->getLanguage()) {
                continue;
            }

            if ($targetSubtype && $targetSubtype != $item->getTargetSubtype()) {
                continue;
            }

            return $item;
        }

        return null;
    }

    /**
     * @return \Pimcore\Model\Metadata\Predefined[]
     */
    public function load(): array
    {
        return $this->getDefinitions();
    }
}
