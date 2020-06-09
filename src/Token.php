<?php


namespace Kumaomao\AuthToken;


use Kumaomao\AuthToken\Contracts\TokenContracts;
use Kumaomao\AuthToken\Exception\TokenException;
use PhpParser\Node\Stmt\Throw_;

class Token implements TokenContracts
{
    /**
     * 设置token
     * @param array $data 存入的数据
     * @param string $token 自定义令牌
     * @return string
     */
    public function set_token(array $data,string $token = ''): string
    {
        $config = config('KmAuth.token');
        $token = $token?:md5(getRandStr(32).$config['salt']);
        if($config['is_auto']){
            $data['expires_time'] = time();
        }
        $res = cache()->set($token,json_encode($data),$config['expires_time']);
        if($res){
            return $token;
        }
        throw new TokenException('生成token失败',500);
    }

    public function get_token(string $token_name = 'token'): string
    {
        // TODO: Implement get_token() method.
    }

    public function ref_token(string $token = ''): string
    {
        // TODO: Implement ref_token() method.
    }

    public function del_token(string $token = ''): bool
    {
        // TODO: Implement del_token() method.
    }

}