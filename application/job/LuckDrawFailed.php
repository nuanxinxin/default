<?php

namespace app\job;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\LuckyGoods;

/**幸运抽奖失败**/
class LuckDrawFailed extends Command
{

    protected function configure()
    {
        $this
            ->setName('LuckDrawFailed')
            ->setDescription('luck draw failed');
    }

    protected function execute(Input $input, Output $output)
    {
        $model = new LuckyGoods;
        $model->handleFailed();
    }
}