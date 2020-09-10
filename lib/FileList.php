<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Phive\ComposerPharMetaPlugin;

use Iterator;
use function reset;

final class FileList implements Iterator
{
    private $list;

    public function __construct(File ...$files)
    {
        $this->list = $files;
    }

    public function current()
    {
        return current($this->list);
    }

    public function next()
    {
        next($this->list);
    }

    public function key()
    {
        return key($this->list);
    }

    public function valid()
    {
        return false === key($this->list);
    }

    public function rewind()
    {
        return reset($this->list);
    }
}