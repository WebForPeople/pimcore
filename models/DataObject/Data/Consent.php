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

use Pimcore\Model\DataObject\OwnerAwareFieldInterface;
use Pimcore\Model\DataObject\Traits\OwnerAwareFieldTrait;
use Pimcore\Model\Element\Note;

class Consent implements OwnerAwareFieldInterface
{
    use OwnerAwareFieldTrait;

    protected bool $consent = false;

    protected ?int $noteId = null;

    protected ?Note $note = null;

    public function __construct(bool $consent = false, ?int $noteId = null)
    {
        $this->consent = $consent;
        $this->noteId = $noteId;
        $this->markMeDirty();
    }

    public function getConsent(): bool
    {
        return $this->consent;
    }

    public function setConsent(bool $consent): void
    {
        if ($consent != $this->consent) {
            $this->consent = $consent;
            $this->markMeDirty();
        }
    }

    public function getNoteId(): ?int
    {
        return $this->noteId;
    }

    public function setNoteId(int $noteId): void
    {
        if ($noteId != $this->noteId) {
            $this->noteId = $noteId;
            $this->markMeDirty();
        }
    }

    public function getNote(): ?Note
    {
        if (empty($this->note) && !empty($this->noteId)) {
            $this->note = Note::getById($this->noteId);
        }

        return $this->note;
    }

    public function setNote(Note $note): void
    {
        $this->note = $note;
        $this->markMeDirty();
    }

    public function getSummaryString(): string
    {
        $note = $this->getNote();
        if ($note) {
            return $note->getTitle() . ': ' . date('r', $note->getDate());
        }

        return '';
    }
}
