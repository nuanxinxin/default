<?php

namespace app\job;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\common\model\LuckyGoodsBuyRecord;

/**幸运抽奖支付超时处理**/
class LuckDrawCancelOrder extends Command
{
    //超时时间(分钟)
    private $overtime = 3;

    protected function configure()
    {
        $this
            ->setName('LuckDrawCancelOrder')
            ->setDescription('luck draw cancel order');
    }

    protected function execute(Input $input, Output $output)
    {
        $condition = array(
            'pay_status' => 0,
            'buy_time' => array('lt', date('Y-m-d H:i:s', strtotime('-' . $this->overtime . ' minute')))
        );
        $update = array(
            'pay_status' => 2
        );
        LuckyGoodsBuyRecord::where($condition)->update($update);
    }
}