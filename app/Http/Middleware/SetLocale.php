<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', 'id');
        
        // Ambil dari query string ?lang=en
        if ($request->has('lang')) {
            $locale = in_array($request->lang, ['id', 'en']) ? $request->lang : 'id';
            $request->session()->put('locale', $locale);
        }
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}
