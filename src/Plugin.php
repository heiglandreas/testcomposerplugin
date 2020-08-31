<?php

declare(strict_types=1);

/**
 * Copyright Andrea Heigl <andreas@heigl.org>
 *
 * Licenses under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org_Heigl\TestComposerPlugin;

use Closure;
use Composer\Composer;
use Composer\DependencyResolver\GenericRule;
use Composer\DependencyResolver\Request;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvent;
use Composer\Installer\InstallerEvents;
use Composer\Installer\PackageEvent;
use Composer\Installer\PackageEvents;
use Composer\IO\IOInterface;
use Composer\Package\CompletePackage;
use Composer\Plugin\PluginInterface;
use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\Constraint\MultiConstraint;
use function get_class;
use function var_dump;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    private const PLUGIN_NAME = 'org_heigl/hyphenator';

    private const BINARY_NAME = 'testbinary';

    private const PHAR_FILE_URI = 'https://github.com/heiglandreas/Org_Heigl_Hyphenator/archive/%version%.zip';

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

    public function preDependenciesSolving(InstallerEvent $event): void
    {
        $this->io->write('test');

        $jobThief = Closure::bind(function & (Request $request) {
            return $request->jobs;
        }, null, $event->getRequest());

        foreach ($event->getRequest()->getJobs() as $key => $job) {
            if (! isset($job['packageName'])) {
                continue;
            }
            if ($job['packageName'] === 'composer/composer') {
                $jobs = & $jobThief($event->getRequest());
                unset($jobs[$key]);
            }
        }
        //$this->io->write(print_r($event->getRequest()->getJobs(), true));
    }

    public function postPackageEvent(PackageEvent $event): void
    {
        /** @var GenericRule $rule */
        $rule = $event->getOperation()->getReason();
        /** @var MultiConstraint $constraint */
        $constraint = $rule->getJob()['constraint'];
        if ($rule->getRequiredPackage() !== self::PLUGIN_NAME) {
            return;
        }

        /** @var CompletePackage $packages */
        $package = $event->getInstalledRepo()->findPackage($rule->getRequiredPackage(), $constraint->getPrettyString());
        $package->getName();

        $this->io->write(sprintf(
            'downloading Artifact in version %2$s from %1$s',
            str_replace('%version%', $package->getFullPrettyVersion(), self::PHAR_FILE_URI),
            $package->getFullPrettyVersion()
        ));

        $this->io->write('downloading signature for PHAR-File');
        $this->io->write('verifying PHAR-File with contained public keys');
        $this->io->write('installing PHAR-File');
        $this->io->write(sprintf('You can now use `./vendor/bin/%1$s` to run the package', self::BINARY_NAME));
    }
}