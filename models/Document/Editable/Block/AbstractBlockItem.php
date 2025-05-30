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

namespace Pimcore\Model\Document\Editable\Block;

use Pimcore\Model\Document;

abstract class AbstractBlockItem
{
    /**
     * @internal
     *
     */
    protected Document\PageSnippet $document;

    /**
     * @internal
     *
     */
    protected array $parentBlockNames;

    /**
     * @internal
     *
     */
    protected int $index;

    public function __construct(Document\PageSnippet $document, array $parentBlockNames, int $index)
    {
        $this->document = $document;
        $this->parentBlockNames = $parentBlockNames;
        $this->index = $index;
    }

    abstract protected function getItemType(): string;

    public function getEditable(string $name): ?Document\Editable
    {
        $id = Document\Editable::buildChildEditableName($name, $this->getItemType(), $this->parentBlockNames, $this->index);
        $editable = $this->document->getEditable($id);

        if ($editable) {
            $editable->setParentBlockNames($this->parentBlockNames);
        }

        return $editable;
    }

    public function __call(string $func, array $args): ?Document\Editable
    {
        $element = $this->getEditable($args[0]);
        $class = 'Pimcore\\Model\\Document\\Editable\\' . str_replace('get', '', $func);

        if ($element !== null && !strcasecmp(get_class($element), $class)) {
            return $element;
        }

        return null;
    }
}
