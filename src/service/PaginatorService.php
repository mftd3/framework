<?php


namespace mftd\service;

use mftd\Paginator;
use mftd\paginator\driver\Bootstrap;
use mftd\Service;

/**
 * 分页服务类
 */
class PaginatorService extends Service
{
    public function boot()
    {
        Paginator::maker(function (...$args) {
            return $this->app->make(Paginator::class, $args, true);
        });

        Paginator::currentPathResolver(function () {
            return $this->app->request->baseUrl();
        });

        Paginator::currentPageResolver(function ($varPage = 'page') {
            $page = $this->app->request->param($varPage);

            if (filter_var($page, FILTER_VALIDATE_INT) !== false && (int)$page >= 1) {
                return (int)$page;
            }

            return 1;
        });
    }

    public function register()
    {
        if (!$this->app->bound(Paginator::class)) {
            $this->app->bind(Paginator::class, Bootstrap::class);
        }
    }
}
