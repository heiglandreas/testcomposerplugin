<?php

declare(strict_types=1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\TestComposerPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvents;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Phive\ComposerPharMetaPlugin\File;
use Phive\ComposerPharMetaPlugin\FileList;
use Phive\ComposerPharMetaPlugin\KeyDirectory;
use Phive\ComposerPharMetaPlugin\Service\Installer;
use Phive\ComposerPharMetaPlugin\Url;
use SplFileInfo;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private const PLUGIN_NAME = 'PluginName';

    private const KEY_FILE_OR_DIRECTORY = __DIR__ . '/../keys/';

    private $composer;

    private $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return [
            InstallerEvents::PRE_DEPENDENCIES_SOLVING => [
                ['preDependenciesSolving', 0],
            ],
            PackageEvents::POST_PACKAGE_INSTALL => [
                ['postPackageEvent', 0],
            ],
            PackageEvents::POST_PACKAGE_UPDATE => [
                ['postPackageEvent', 0],
            ],
        ];
    }

    public function postPackageEvent(PackageEvent $event): void
    {
        $handler = new Installer(
            self::PLUGIN_NAME,
            $this->io,
            new KeyDirectory(new SplFileInfo(self::KEY_FILE_OR_DIRECTORY)),
            $event
        );

        $handler->install(new FileList(new File(
            'captainhook',
            Url::fromString('https://github.com/CaptainHookPhp/captainhook/releases/download/%version%/captainhook.phar'),
            Url::fromString('https://github.com/CaptainHookPhp/captainhook/releases/download/%version%/captainhook.phar.asc'),
        )));
    }
}