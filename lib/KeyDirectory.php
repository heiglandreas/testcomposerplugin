<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Phive\ComposerPharMetaPlugin;

use DirectoryIterator;
use SplFileInfo;

final class KeyDirectory
{
    private $keys;

    public function __construct(SplFileInfo $publicKeyFolder)
    {
        $this->keys = [];

        if (! $publicKeyFolder->isDir()) {
            $this->keys = $publicKeyFolder;
            return;
        }

        foreach (new DirectoryIterator($publicKeyFolder->getPathname()) as $item) {
            if (! $item->isFile()) {
                continue;
            }

            $this->keys[] = $item;
        }
    }
}