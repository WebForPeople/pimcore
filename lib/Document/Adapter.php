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

namespace Pimcore\Document;

use Exception;
use Pimcore\Model\Asset;

/**
 * @internal
 */
abstract class Adapter
{
    protected ?Asset\Document $asset = null;

    protected array $tmpFiles = [];

    protected function removeTmpFiles(): void
    {
        // remove tmp files
        if (!empty($this->tmpFiles)) {
            foreach ($this->tmpFiles as $tmpFile) {
                if (file_exists($tmpFile)) {
                    unlink($tmpFile);
                }
            }
        }
    }

    public function __destruct()
    {
        $this->removeTmpFiles();
    }

    abstract public function load(Asset\Document $asset): static;

    abstract public function saveImage(string $imageTargetPath, int $page = 1, int $resolution = 200): bool;

    /**
     * @return resource
     *
     * @throws Exception
     */
    abstract public function getPdf(?Asset\Document $asset = null);

    abstract public function isFileTypeSupported(string $fileType): bool;

    /**
     * @throws Exception
     */
    abstract public function getPageCount(): int;

    abstract public function getText(?int $page = null, ?Asset\Document $asset = null): mixed;
}
