<?php

  namespace App\Providers;

  use Illuminate\Support\ServiceProvider;
  use Illuminate\Support\Facades\Route;

  class AppServiceProvider extends ServiceProvider
  {
      public function boot()
      {
          Route::aliasMiddleware('auth', \App\Http\Middleware\Authenticate::class);
          Route::aliasMiddleware('role', \App\Http\Middleware\Authenticate::class);
      }
  }