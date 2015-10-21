<?php namespace LaravelCaptcha\Config;

use Illuminate\Foundation\Application as Laravel;

final class FrameworkInformation {

    // disable instance creation
  	private function __construct() {}


  	public static function GetFrameworkVersion() {
      return Laravel::VERSION;
  	}


  	public static function GetControllersPath() {
  		$controllersPath = '';

  		$currentVersion = FrameworkInformation::GetFrameworkVersion();

  		if (version_compare($currentVersion, '5.0', '<')) {
        // laravel 4.x
  			$controllersPath =  app_path() . '/controllers';
  		} else {
  			// laravel 5.x
  			$controllersPath =  app_path() . '/Http/Controllers';
  		}

  		return $controllersPath;
  	}


    public static function GetBaseUrl() {
      return \URL::to('/');
    }
}
