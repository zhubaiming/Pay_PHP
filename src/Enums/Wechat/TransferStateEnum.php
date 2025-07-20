<?php

namespace Hongyi\Pay\Enums\Wechat;

enum TransferStateEnum: int
{
    //-------------------------------------------------------------------------------------------------------------------
    // 转账订单状态        | 状态说明
    // ACCEPTED          | 转账已受理
    // PROCESSING        | 转账锁定资金中，如果一直停留在改状态，建议检查账户余额是否足够，如余额不足，可充值后再原单重试
    // WAIT_USER_CONFIRM | 待收款用户确认，可拉起微信收款确认页面进行收款确认
    // TRANSFERING       | 转账中，可拉起微信收款确认页面在此重试确认收款
    // SUCCESS           | 转账成功
    // FAIL              | 转账失败
    // CANCELING         | 商户撤销请求受理成功，该笔转账正在撤销中
    // CANCELLED         | 转账撤销完成
    //-------------------------------------------------------------------------------------------------------------------
    case ACCEPTED = 0;
    case PROCESSING = 1;
    case WAIT_USER_CONFIRM = 2;
    case TRANSFERING = 4;
    case SUCCESS = 5;
    case FAIL = 6;
    case CANCELING = 7;
    case CANCELLED = 8;

    public function name(string $type = null)
    {
        return match ($this) {
            self::ACCEPTED => '转账已受理',
            self::PROCESSING => '转账锁定资金中，如余额不足',
            self::WAIT_USER_CONFIRM => '待收款用户确认',
            self::TRANSFERING => '转账中',
            self::SUCCESS => '转账成功',
            self::FAIL => '转账失败',
            self::CANCELING => '撤销转账受理中',
            self::CANCELLED => '转账撤销'
        };
    }

    public static function isFail(): array
    {
        return [
            self::PROCESSING->name,
            self::FAIL->name
        ];
    }
}
