<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Using "*" trusts all proxies which is appropriate when your
     * application is behind a load balancer / ingress that correctly
     * sets X-Forwarded-* headers (common in Docker / Kubernetes setups).
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * Symfony 7 removed the HEADER_X_FORWARDED_ALL constant.
     * Use the explicit bitmask of supported X-Forwarded headers instead.
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO;
}
