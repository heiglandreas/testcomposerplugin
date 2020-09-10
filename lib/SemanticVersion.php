<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Phive\ComposerPharMetaPlugin;

use Phive\ComposerPharMetaPlugin\Exception\NoSemanticVersioning;

final class SemanticVersion
{
    private $major;

    private $minor;

    private $patch;

    private $build;

    private function __construct(int $major, int $minor, int $patch, string $build)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->build = $build;
    }

    public static function fromVersionString(string $versionString): self
    {
        $a = explode('-', $versionString);
        $build = '';
        if (isset($a[1])) {
            $build = $a[1];
        }

        $b = explode('.', $a[0]);
        if (! $b) {
            throw NoSemanticVersioning::fromversionString($versionString);
        }

        if (3 !== count($b)) {
            throw NoSemanticVersioning::fromversionString($versionString);
        }

        return new self((int) $b[0], (int) $b[1], (int) $b[2], $build);
    }

    public function major(): int
    {
        return $this->major;
    }

    public function minor(): int
    {
        return $this->minor;
    }

    public function patch(): int
    {
        return $this->patch;
    }

    public function build(): string
    {
        return $this->build;
    }
}