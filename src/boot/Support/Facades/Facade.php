<?php

namespace Boot\Support\Facades;

use Boot\Support\Str;
use Boot\Models\Model;
use Boot\Support\Numb;
use Illuminate\Support\Js;
use Illuminate\Support\Arr;
use Boot\Support\Facades\Mail;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Facade as LaravelFacade;

abstract class Facade extends LaravelFacade
{
    public static function defaultAliases()
    {
        return collect([
            'App' => App::class,
            'Arr' => Arr::class,
            'Auth' => Auth::class,
            'Blade' => Blade::class,
            'Broadcast' => Broadcast::class,
            'Bus' => Bus::class,
            'Cache' => Cache::class,
            'Config' => Config::class,
            'Cookie' => Cookie::class,
            'Crypt' => Crypt::class,
            'Date' => Date::class,
            'DB' => DB::class,
            'Eloquent' => Model::class,
            'Event' => Event::class,
            'File' => File::class,
            'Gate' => Gate::class,
            'Hash' => Hash::class,
            'Http' => Http::class,
            'Js' => Js::class,
            'Lang' => Lang::class,
            'Log' => Log::class,
            'Mail' => Mail::class,
            'Notification' => Notification::class,
            'Numb' => Numb::class,
            'Number' => Number::class,
            'Password' => Password::class,
            'Queue' => Queue::class,
            'RateLimiter' => RateLimiter::class,
            'Redirect' => Redirect::class,
            'Request' => Request::class,
            'Response' => Response::class,
            'Route' => Route::class,
            'Schema' => Schema::class,
            'Session' => Session::class,
            'Storage' => Storage::class,
            'Str' => Str::class,
            'URL' => URL::class,
            'Validator' => Validator::class,
            'View' => View::class,
        ]);
    }
}
