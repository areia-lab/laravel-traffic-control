<?php

namespace AreiaLab\TrafficControl\Rules;

use Illuminate\Http\Request;

class GeoBlockRule
{
    public function handle(Request $request)
    {
        $country = $this->lookupCountry($request->ip());
        if (in_array($country, ['CN', 'RU'])) {
            return response('Not available in your region', 403);
        }
        return true;
    }

    protected function lookupCountry($ip)
    {
        return 'US';
    }
}
