<?php

namespace QuantaQuirk\Http\Middleware;

use QuantaQuirk\Support\Collection;
use QuantaQuirk\Support\Facades\Vite;

class AddLinkHeadersForPreloadedAssets
{
    /**
     * Handle the incoming request.
     *
     * @param  \QuantaQuirk\Http\Request  $request
     * @param  \Closure  $next
     * @return \QuantaQuirk\Http\Response
     */
    public function handle($request, $next)
    {
        return tap($next($request), function ($response) {
            if (Vite::preloadedAssets() !== []) {
                $response->header('Link', Collection::make(Vite::preloadedAssets())
                    ->map(fn ($attributes, $url) => "<{$url}>; ".implode('; ', $attributes))
                    ->join(', '));
            }
        });
    }
}
