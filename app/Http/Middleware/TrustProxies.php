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
     */
    protected $headers = Request::HEADER_X_FORWARDED_ALL | Request::HEADER_X_FORWARDED_AWS_ELB;
}

