<?php

namespace app\job;

use think\console\Command;
use think\console\Input;
use think\console\Output;

/**幸运抽奖支付超时处理**/
class UserCreditVoucher extends Command
{
    //超时时间(分钟)
    private $overtime = 3;

    protected function configure()
    {
        $this
            ->setName('UserCreditVoucher')
            ->setDescription('userCreditVoucher order');
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