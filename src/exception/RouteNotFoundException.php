<?php


namespace mftd\exception;

/**
 * 路由未定义异常
 */
class RouteNotFoundException extends HttpException
{
    public function __construct()
    {
        parent::__construct(404, 'Route Not Found');
    }
}
