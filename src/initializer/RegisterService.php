<?php


namespace mftd\initializer;

use mftd\App;
use mftd\service\ModelService;
use mftd\service\PaginatorService;
use mftd\service\ValidateService;

/**
 * 注册系统服务
 */
class RegisterService
{
    protected $services = [
        PaginatorService::class,
        ValidateService::class,
        ModelService::class,
    ];

    public function init(App $app)
    {
        $file = $app->getRootPath() . 'vendor/services.php';

        $services = $this->services;

        if (is_file($file)) {
            $services = array_merge($services, include $file);
        }

        foreach ($services as $service) {
            if (class_exists($service)) {
                $app->register($service);
            }
        }
    }
}
