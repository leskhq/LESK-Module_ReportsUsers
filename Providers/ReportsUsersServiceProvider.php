<?php
namespace App\Modules\ReportsUsers\Providers;

use App;
use Config;
use Lang;
use View;
use Illuminate\Support\ServiceProvider;

class ReportsUsersServiceProvider extends ServiceProvider
{
	/**
	 * Register the ReportsUsers module service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// This service provider is a convenient place to register your modules
		// services in the IoC container. If you wish, you may make additional
		// methods or service providers to keep the code more focused and granular.
		App::register('App\Modules\ReportsUsers\Providers\RouteServiceProvider');

		$this->registerNamespaces();
	}

	/**
	 * Register the ReportsUsers module resource namespaces.
	 *
	 * @return void
	 */
	protected function registerNamespaces()
	{
		Lang::addNamespace('reports_users', realpath(__DIR__.'/../Resources/Lang'));

		View::addNamespace('reports_users', base_path('resources/views/vendor/reports_users'));
		View::addNamespace('reports_users', realpath(__DIR__.'/../Resources/Views'));
	}

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
		// $this->addMiddleware('');

        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('reports_users.php'),
        ], 'config');

        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'reports_users'
        );
    }

	/**
     * Register the Middleware
     *
     * @param  string $middleware
     */
	protected function addMiddleware($middleware)
	{
		$kernel = $this->app['Illuminate\Contracts\Http\Kernel'];
        $kernel->pushMiddleware($middleware);
	}
}
