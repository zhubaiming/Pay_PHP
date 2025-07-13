<?php

declare(strict_types=1);

namespace Hongyi\Pay\Enums\Wechat;

use Hongyi\Designer\Contracts\HttpEnumInterface;

enum HttpEnum: int implements HttpEnumInterface
{
    //-------------------------------------------------------------------------------------------------------------------
    // 状态码                     | 错误类型                  | 一般的解决方案                          | 典型错误码示例(code)
    // 200 - OK                  | 处理成功                  | /                                     | /
    // 202 - Accepted            | 服务器已接受请求，但尚未处理 | 请使用原参数重复请求一遍                  | /
    // 204 - No Content          | 处理成功，无返回Body       | /                                     | /
    // 400 - Bad Request         | 协议或者参数非法           | 请根据接口返回的详细信息检查您的程序        | PARAM_ERROR
    // 401 - Unauthorized        | 签名验证失败              | 请检查签名参数和方法是否都符合签名算法要求   | SIGN_ERROR
    // 403 - Forbidden           | 权限异常                  | 请开通商户号相关权限。请联系产品或商务申请   | NO_AUTH
    // 404 - Not Found           | 请求的资源不存在           | 请商户检查需要查询的ID或者请求URL是否正确   | ORDER_NOT_EXIST
    // 405 - Method Not Allowed  | 请求方式不正确             | 请商户检查使用的请求方式是否符合接口文档要求 | /
    // 429 - Too Many Requests   | 请求超过频率限制           | 请求未受理，请降低频率后重试               | RATELIMIT_EXCEEDED
    // 500 - Server Error        | 系统错误                  | 按具体接口的错误指引进行重试               | SYSTEM_ERROR
    // 502 - Bad Gateway         | 服务下线，暂时不可用        | 请求无法处理，请稍后重试                  | SERVICE_UNAVAILABLE
    // 503 - Service Unavailable | 服务不可用，过载保护        | 请求无法处理，请稍后重试                  | SERVICE_UNAVAILABLE
    //-------------------------------------------------------------------------------------------------------------------
    case OK = 200;
    case ACCEPTED = 202;
    case NO_CONTENT = 204;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case TOO_MANY_REQUESTS = 429;
    case SERVER_ERROR = 500;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;

    public static function isSuccess(int $code): bool
    {
        return match ($code) {
            self::OK->value, self::ACCEPTED->value, self::NO_CONTENT->value => true,
            default => false
        };
    }

    // 获取状态码描述
    public function getMessage(): string
    {
        return match ($this) {
            self::OK => '处理成功',
            self::ACCEPTED => '服务器已接受请求，但尚未处理',
            self::NO_CONTENT => '处理成功，无返回Body',
            self::BAD_REQUEST => '协议或者参数非法',
            self::UNAUTHORIZED => '签名验证失败',
            self::FORBIDDEN => '权限异常',
            self::NOT_FOUND => '请求的资源不存在',
            self::METHOD_NOT_ALLOWED => '请求方式不正确',
            self::TOO_MANY_REQUESTS => '请求超过频率限制',
            self::SERVER_ERROR => '系统错误',
            self::BAD_GATEWAY => '服务下线，暂时不可用',
            self::SERVICE_UNAVAILABLE => '服务不可用，过载保护',
            default => throw new \Exception('Unexpected match value')
        };
    }

    // 获取解决方案
    public function getSolution(): string
    {
        return match ($this) {
            self::OK, self::NO_CONTENT => '/',
            self::ACCEPTED => '请使用原参数重复请求一遍',
            self::BAD_REQUEST => '请根据接口返回的详细信息检查您的程序',
            self::UNAUTHORIZED => '请检查签名参数和方法是否符合签名算法要求',
            self::FORBIDDEN => '请开通商户号相关权限。请联系产品或商务申请',
            self::NOT_FOUND => '请商户检查需要查询的ID或者请求URL是否正确',
            self::METHOD_NOT_ALLOWED => '请商户检查使用的请求方式是否符合接口文档要求',
            self::TOO_MANY_REQUESTS => '请求未受理，请降低频率后重试',
            self::SERVER_ERROR, self::BAD_GATEWAY, self::SERVICE_UNAVAILABLE => '请求无法处理，请稍后重试',
            default => throw new \Exception('Unexpected match value'),
        };
    }

    // 获取典型错误码
    public function getErrorCode(): ?string
    {
        return match ($this) {
            self::BAD_REQUEST => 'PARAM_ERROR',
            self::UNAUTHORIZED => 'SIGN_ERROR',
            self::FORBIDDEN => 'NO_AUTH',
            self::NOT_FOUND => 'ORDER_NOT_EXIST',
            self::TOO_MANY_REQUESTS => 'RATELIMIT_EXCEEDED',
            self::SERVER_ERROR, self::BAD_GATEWAY, self::SERVICE_UNAVAILABLE => 'SERVICE_UNAVAILABLE',
            default => null,
        };
    }
}
