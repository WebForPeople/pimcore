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

namespace Pimcore\Tests\Support\Test;

use Pimcore\Model\Asset;
use Pimcore\Model\Element\ElementInterface;
use Pimcore\Model\Element\Service;
use Pimcore\Tests\Support\Helper\Element\PropertiesTestHelper;
use Pimcore\Tests\Support\Util\TestHelper;

abstract class AbstractPropertiesTest extends ModelTestCase
{
    protected bool $cleanupDbInSetup = true;

    protected PropertiesTestHelper $propertiesTestHelper;

    protected ElementInterface $testElement;

    public function _inject(PropertiesTestHelper $testHelper): void
    {
        $this->propertiesTestHelper = $testHelper;
    }

    abstract public function createElement(): ElementInterface;

    abstract public function reloadElement(): ElementInterface;

    protected function needsDb(): bool
    {
        return true;
    }

    public function testCRUD(): void
    {
        // create and read
        $this->createElement();
        $expectedData = 'sometext' . uniqid();
        $this->testElement->setProperty('textproperty1', 'input', $expectedData . '_1');
        $this->testElement->setProperty('textproperty2', 'input', $expectedData . '_2');
        $this->testElement->save();

        $this->reloadElement();
        $this->assertTrue($this->testElement->hasProperty('textproperty1'));
        $actual = $this->testElement->getProperty('textproperty1');

        $this->assertEquals($expectedData . '_1', $actual);

        $actual = $this->testElement->getProperty('textproperty2');
        $expectedData2 = $expectedData;
        $this->assertEquals($expectedData . '_2', $actual);

        // update
        $expectedData = 'sometext' . uniqid() . '_new';
        $this->testElement->setProperty('textproperty1', 'input', $expectedData);
        $this->testElement->save();
        $this->reloadElement();
        $actual = $this->testElement->getProperty('textproperty1');
        $this->assertEquals($expectedData, $actual);

        // delete
        $this->testElement->setProperty('textproperty1', 'input', null);
        $this->testElement->save();

        $this->reloadElement();
        $actual = $this->testElement->getProperty('textproperty1');
        $this->assertEquals(null, $actual);

        $expectedData = 'sometext' . uniqid();
        $actual = $this->testElement->getProperty('textproperty2');
        $this->assertEquals($expectedData2 . '_2', $actual);
    }

    public function testInheritance(): void
    {
        // create and read
        $parentElement = $this->createElement();
        $childElement = $this->createElement();
        $childElement->setParentId($parentElement->getId());
        $childElement->save();
        $this->testElement = $parentElement;

        $expectedData = 'sometext' . uniqid();
        $this->testElement->setProperty('textproperty3', 'input', $expectedData . '_3', false, true);
        $this->testElement->save();

        $childElement = Service::getElementById(Service::getElementType($childElement), $childElement->getId(), ['force' => true]);
        $this->assertEquals($expectedData . '_3', $childElement->getProperty('textproperty3'));
    }

    public function testRelation(): void
    {
        $asset = TestHelper::createImageAsset();

        $this->createElement();
        $this->testElement->setProperty('assetProperty', 'asset', $asset);
        $this->testElement->save();
        $this->reloadElement();

        /** @var Asset $assetProperty */
        $assetProperty = $this->testElement->getProperty('assetProperty');
        $this->assertInstanceOf(Asset::class, $assetProperty);
        $this->assertEquals($asset->getId(), $assetProperty->getId());
    }
}
