<?php

namespace app\job;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\LuckyDaySettlement;

/**幸运抽奖日报表统计**/
class LuckDrawDayCount extends Command
{

    protected function configure()
    {
        $this
            ->setName('LuckDrawDayCount')
            ->setDescription('luck draw day count');
    }

    protected function execute(Input $input, Output $output)
    {
        $model = new LuckyDaySettlement;
        $model->everydayCount();
    }
}