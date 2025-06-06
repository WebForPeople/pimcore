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

namespace Pimcore\Event\Model\Document;

use Pimcore\Document\Editable\Block\BlockState;
use Pimcore\Model\Document;
use Symfony\Contracts\EventDispatcher\Event;

class EditableNameEvent extends Event
{
    /**
     * Editable type (e.g. "input")
     *
     */
    private string $type;

    /**
     * Editable name (e.g. "headline")
     *
     */
    private string $inputName;

    /**
     * The current block state
     *
     */
    private BlockState $blockState;

    /**
     * The built editable name
     *
     */
    private string $editableName;

    private Document $document;

    public function __construct(
        string $type,
        string $inputName,
        BlockState $blockState,
        string $editableName,
        Document $document
    ) {
        $this->type = $type;
        $this->inputName = $inputName;
        $this->blockState = $blockState;
        $this->editableName = $editableName;
        $this->document = $document;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getInputName(): string
    {
        return $this->inputName;
    }

    public function getBlockState(): BlockState
    {
        return $this->blockState;
    }

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getEditableName(): string
    {
        return $this->editableName;
    }

    public function setEditableName(string $editableName): void
    {
        $this->editableName = $editableName;
    }
}
