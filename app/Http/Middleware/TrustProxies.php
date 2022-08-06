<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Fideloper\Proxy\TrustProxies as Middleware;

/**
 * Standard Laravel middleware class.
 */
class TrustProxies extends Middleware
{

    /**
     * The trusted proxies for this application.
     *
     * @var array|string
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var integer
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
      Request::HEADER_X_FORWARDED_HOST |
      Request::HEADER_X_FORWARDED_PORT |
      Request::HEADER_X_FORWARDED_PROTO |
      Request::HEADER_X_FORWARDED_AWS_ELB;
}
