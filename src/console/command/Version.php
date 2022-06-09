<?php

declare(strict_types=1);

namespace think\console\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Version extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('version')
            ->setDescription('show thinkphp framework version');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('v' . $this->app->version());
    }
}
