<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoveWwwPrefix
{
    public function handle(Request $request, Closure $next)
    {
        if (str_starts_with($request->getHost(), 'www.')) {
            $host = str_replace('www.', '', $request->getHost());
            $url = $request->getScheme() . '://' . $host . $request->getRequestUri();

            return redirect()->to($url, 301); // permanent redirect
        }

        return $next($request);
    }
}
