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

namespace Pimcore\Model\DataObject\Data;

use Exception;
use InvalidArgumentException;
use Pimcore;
use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\DataObject\QuantityValue\Unit;
use Pimcore\Model\DataObject\QuantityValue\UnitConversionService;
use Pimcore\Model\DataObject\Traits\ObjectVarTrait;
use Pimcore\Model\DataObject\Traits\OwnerAwareFieldTrait;
use Pimcore\Model\Exception\NotFoundException;

abstract class AbstractQuantityValue implements OwnerAwareFieldInterface
{
    use ObjectVarTrait;
    use OwnerAwareFieldTrait;

    protected string|null $unitId = null;

    protected ?Unit $unit = null;

    /**
     * @throws NotFoundException
     */
    public function __construct(Unit|string|null $unit = null)
    {
        if ($unit instanceof Unit) {
            $this->unit = $unit;
        } elseif ($unit) {
            $existingUnit = Unit::getById($unit);
            if ($existingUnit) {
                $this->unit = $existingUnit;
            } else {
                throw new NotFoundException('Unit ' . $unit . ' was not found.');
            }
        }
        $this->unitId = $this->unit?->getId();
        $this->markMeDirty();
    }

    /**
     * @throws NotFoundException
     */
    public function setUnitId(string $unitId): void
    {
        $this->unitId = null;
        $this->unit = null;
        $unit = Unit::getById($unitId);

        if ($unit) {
            $this->unitId = $unitId;
            $this->unit = $unit;
        } else {
            throw new NotFoundException('Unit Id ' . $unitId . ' was not found.');
        }
        $this->markMeDirty();
    }

    public function getUnitId(): string|null
    {
        return $this->unitId;
    }

    public function getUnit(): ?Unit
    {
        if (empty($this->unit) && !empty($this->unitId)) {
            $this->unit = Unit::getById($this->unitId);
        }

        return $this->unit;
    }

    /**
     * @param string|Unit $unit target unit. if string provided, unit is tried to be found by abbreviation
     *
     * @throws Exception
     */
    public function convertTo(Unit|string $unit): AbstractQuantityValue
    {
        if (is_string($unit)) {
            $unitObject = Unit::getByAbbreviation($unit);
            if (!$unitObject instanceof Unit) {
                throw new InvalidArgumentException('Unit with abbreviation "'.$unit.'" does not exist');
            }
            $unit = $unitObject;
        }

        /** @var UnitConversionService $converter */
        $converter = Pimcore::getContainer()->get(UnitConversionService::class);

        return $converter->convert($this, $unit);
    }

    abstract public function getValue(): mixed;

    abstract public function __toString(): string;
}
