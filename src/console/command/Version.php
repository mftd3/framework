<?php


namespace mftd\console\command;

use mftd\console\Command;
use mftd\console\Input;
use mftd\console\Output;

class Version extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('version')
            ->setDescription('show mftdphp framework version');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('v' . $this->app->version());
    }
}
