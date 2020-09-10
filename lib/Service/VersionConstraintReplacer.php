<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Phive\ComposerPharMetaPlugin\Service;

use Phive\ComposerPharMetaPlugin\PackageVersion;

final class VersionConstraintReplacer
{
    private $versionConstraint;

    public function __construct(PackageVersion $versionConstraint)
    {
        $this->versionConstraint = $versionConstraint;
    }

    public function replace(string $string): string
    {
        return str_replace([
            '%minor%',
            '%major%',
            '%patch%',
            '%release%',
            '%version%',
        ], [
            $this->versionConstraint->minor(),
            $this->versionConstraint->major(),
            $this->versionConstraint->patch(),
            $this->versionConstraint->build(),
            $this->versionConstraint->fullVersion(),
        ], $string);
    }
}