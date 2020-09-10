<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Phive\ComposerPharMetaPlugin\Service;


use Phive\ComposerPharMetaPlugin\KeyDirectory;
use SplFileInfo;

final class Verify
{
    /** @var SplFileInfo[] */
    private $keys;

    public function __construct(KeyDirectory $keys)
    {
        $this->keys = $keys;
    }

    public function fileWithSignature(SplFileInfo $file, SplFileInfo $signature): bool
    {
        return true;
    }
}