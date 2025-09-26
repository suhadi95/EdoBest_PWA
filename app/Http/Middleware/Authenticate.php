<?php

  namespace App\Http\Middleware;

  use Closure;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Session;

  class Authenticate
  {
      public function handle(Request $request, Closure $next, $role = null)
      {
          if (!Session::has('user')) {
              Session::flash('error', 'Harap login terlebih dahulu.');
              return redirect()->route('login');
          }

          if ($role && Session::get('user')->role !== $role) {
              Session::flash('error', 'Akses ditolak. Role tidak sesuai.');
              return redirect()->route('login');
          }

          return $next($request);
      }
  }