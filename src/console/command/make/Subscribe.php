<?php

namespace think\console\command\make;

use think\console\command\Make;

class Subscribe extends Make
{
    protected $type = "Subscribe";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:subscribe')
            ->setDescription('Create a new subscribe class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'subscribe.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\subscribe';
    }
}
