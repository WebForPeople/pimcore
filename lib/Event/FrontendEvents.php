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

namespace Pimcore\Event;

final class FrontendEvents
{
    /**
     * Allows to rewrite the frontend path of an image thumbnail
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: Pimcore\Model\Asset\Image\Thumbnail
     * Arguments:
     *  - filesystemPath | string | Absolute path of the thumbnail on the filesystem
     *  - frontendPath | string | Web-path, relative
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const ASSET_IMAGE_THUMBNAIL = 'pimcore.frontend.path.asset.image.thumbnail';

    /**
     * Allows to rewrite the frontend path of an video image thumbnail
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: Pimcore\Model\Asset\Video\ImageThumbnail
     * Arguments:
     *  - filesystemPath | string | Absolute path of the thumbnail on the filesystem
     *  - frontendPath | string | Web-path, relative
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const ASSET_VIDEO_IMAGE_THUMBNAIL = 'pimcore.frontend.path.asset.video.image-thumbnail';

    /**
     * Allows to rewrite the frontend path of an video thumbnail (mp4)
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: Pimcore\Model\Asset\Video
     * Arguments:
     *  - filesystemPath | string | Absolute path of the thumbnail on the filesystem
     *  - frontendPath | string | Web-path, relative
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const ASSET_VIDEO_THUMBNAIL = 'pimcore.frontend.path.asset.video.thumbnail';

    /**
     * Allows to rewrite the frontend path of an video thumbnail (mp4)
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: 	Pimcore\Model\Asset\Document\ImageThumbnail
     * Arguments:
     *  - filesystemPath | string | Absolute path of the thumbnail on the filesystem
     *  - frontendPath | string | Web-path, relative
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const ASSET_DOCUMENT_IMAGE_THUMBNAIL = 'pimcore.frontend.path.asset.document.image-thumbnail';

    /**
     * Allows to rewrite the frontend path of an asset (no matter which type)
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: 	Pimcore\Model\Asset
     * Arguments:
     *  - frontendPath | string | Web-path, relative
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const ASSET_PATH = 'pimcore.frontend.path.asset';

    /**
     * Allows to rewrite the frontend path of a document (no matter which type)
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: 	Pimcore\Model\Document
     * Arguments:
     *  - frontendPath | string | Web-path, relative
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const DOCUMENT_PATH = 'pimcore.frontend.path.document';

    /**
     * Allows to rewrite the frontend path of a static route
     * Overwrite the argument "frontendPath" to do so
     *
     * Subject: 	Pimcore\Bundle\StaticRoutesBundle\Model\Staticroute
     * Arguments:
     *  - frontendPath | string | Web-path, relative
     *  - params | array
     *  - reset | bool
     *  - encode | bool
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const STATICROUTE_PATH = 'pimcore.frontend.path.staticroute';

    /**
     * Subject: 	\Pimcore\Twig\Extension\Templating\HeadLink
     * Arguments:
     *  - item | stdClass
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const VIEW_HELPER_HEAD_LINK = 'pimcore.frontend.view.helper.head-link';

    /**
     * Subject: 	\Pimcore\Twig\Extension\Templating\HeadScript
     * Arguments:
     *  - item | stdClass
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const VIEW_HELPER_HEAD_SCRIPT = 'pimcore.frontend.view.helper.head-script';
}
