<?php

namespace Luinuxscl\LnxSam;

use Illuminate\Support\ServiceProvider;

/**
 * Registers the runtime of the platform agent.
 *
 * The package owns no identity and no surface: the product package declares
 * Sam, the modules declare what they contribute and the host application
 * provides the model gateway, the identities and the audit store.
 */
class LnxSamServiceProvider extends ServiceProvider
{
    /**
     * Legacy container keys the host used before the runtime was extracted.
     *
     * @var array<class-string, list<string>>
     */
    private const LEGACY_KEYS = [
        SamRegistry::class => ['App\Ai\Sam\SamRegistry'],
        SamAccess::class => ['App\Ai\Sam\SamAccess'],
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/lnx_sam.php', 'lnx_sam');

        $this->app->singleton(SamRegistry::class);
        $this->app->singleton(SamAccess::class);

        // The runtime lived in the host application as `App\Ai\Sam`. Both keys
        // resolve to the same instance so an application that still resolves
        // the old name never builds a second, empty registry.
        foreach (self::LEGACY_KEYS as $class => $keys) {
            foreach ($keys as $key) {
                $this->app->alias($class, $key);
            }
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\SamCapabilitiesCommand::class,
                Console\Commands\SamDescribeCommand::class,
                Console\Commands\SamRunCommand::class,
                Console\Commands\SamDoctorCommand::class,
            ]);
        }
    }
}
