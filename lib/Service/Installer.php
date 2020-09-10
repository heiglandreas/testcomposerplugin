<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Phive\ComposerPharMetaPlugin\Service;

use Composer\Installer\PackageEvent;
use Composer\IO\IOInterface;
use Phive\ComposerPharMetaPlugin\Exception\SomebodyElsesProblem;
use Phive\ComposerPharMetaPlugin\File;
use Phive\ComposerPharMetaPlugin\FileList;
use Phive\ComposerPharMetaPlugin\KeyDirectory;
use Phive\ComposerPharMetaPlugin\PackageVersion;
use Phive\ComposerPharMetaPlugin\Url;
use RuntimeException;
use SplFileInfo;
use function sprintf;
use function str_replace;
use function sys_get_temp_dir;

final class Installer
{
    private $io;

    private $name;

    private $keys;

    private $event;

    public function __construct(string $name, IOInterface $io, KeyDirectory $keys, PackageEvent $event)
    {
        $this->name = $name;
        $this->io = $io;
        $this->keys = $keys;
        $this->event = $event;
    }

    public function install(FileList $filelist): void
    {
        try {
            $packageVersion = PackageVersion::fromPackageEvent($this->event, $this->name);
        } catch (SomebodyElsesProblem $e) {
            return;
        }
        $versionReplacer = new VersionConstraintReplacer($packageVersion);


        /** @var File $file */
        foreach ($filelist as $file) {
            $this->io->write(sprintf(
                'downloading Artifact in version %2$s from %1$s',
                $versionReplacer->replace( $file->pharUrl()->toString()),
                $packageVersion->fullVersion()
            ));
            $download = new Download(Url::fromString(
                $versionReplacer->replace($file->pharUrl()->toString())
            ));
            $pharLocation = new SplFileInfo(sys_get_temp_dir() . '/' . $file->pharName());
            $download->toLocation($pharLocation);

            $downloadSignature = new Download(Url::fromString(
                $versionReplacer->replace($file->signatureUrl()->toString())
            ));
            $signatureLocation = new SplFileInfo(sys_get_temp_dir() . '/' . $file->pharName() . '.asc');
            $downloadSignature->toLocation($signatureLocation);

            $verify = new Verify($this->keys);
            if (! $verify->fileWithSignature($pharLocation, $signatureLocation)) {
                throw new RuntimeException('Signature Verification failed');
            }
        }
    }
}