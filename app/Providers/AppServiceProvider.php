<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Set default DB string length
		Schema::defaultStringLength(160);

		// Override functions Blueprint
		Blueprint::macro('customSoftDeletes', function ($column = 'deleted_at', $precision = 0) {
			return $this->dateTime($column, $precision)->nullable();
		});

		Blueprint::macro('customTimestamps', function ($precision = 0) {
			$this->dateTime('created_at', $precision)->nullable();
			$this->dateTime('updated_at', $precision)->nullable();
		});
	}
}
