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
use Composer\DependencyResolver\Request;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Installer\InstallerEvent;
use Composer\Installer\InstallerEvents;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
{
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
        $this->io->write(print_r($event->getRequest()->getJobs(), true));
    }


}