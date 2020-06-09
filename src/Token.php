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

    /**
     * 获取客户端发送的token
     * @param string $token_name 默认token
     * @return string
     */
    public function get_token(string $token_name = 'token'): string
    {
        $token = false;
        //获取携带token header,cookie,params
        $cookies = request()->getCookieParams();
        if(isset($cookies[$token_name]) && $cookies[$token_name]){
            $token = $cookies[$token_name];
            return $token;
        }
        $header = request()->getHeaders();
        if(isset($header[$token_name]) && $header[$token_name]){
            $token = $header[$token_name];
            return $token;
        }
        $token = request()->input($token_name);
        if($token){
            return $token;
        }
       throw new TokenException('非法访问',500);
    }


    /**
     * 刷新token
     * @param string $token 需要刷新的token
     * @param bool $is_old 是否保持token不变
     * @return string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function ref_token(string $token = '',bool $is_old=false): string
    {
        $token = $token?:$this->get_token();
        $data = cache()->get($token);
        if($is_old){
            $token = $this->set_token($data,$token);
        }else{
            $token = $this->set_token($data);
        }
       return $token;
    }

    /**
     * 删除对应token
     * @param string $token
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function del_token(string $token = ''): bool
    {
        $token = $token?:$this->get_token();
        $res = cache()->delete($token);
        return $res?true:false;
    }

    /**
     * 验证token
     * @param string $token
     * @return bool|string
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function check_token(string $token = '')
    {
        $token = $token?:$this->get_token();
        $existe = cache()->has($token);
        if(!$existe){
           throw new TokenException('非法访问',401);
        }
        //判断是否开启自动更新
        if(config('KmAuth.token.is_auto')){
            $info = cache()->get($token);
            $time = $info['expires_time'];
            if(time() - $time < config('KmAuth.token.refresh_time')){
                //重置token
                cache()->delete($token);
                $token = $this->setToken($info);
                return $token;
            }
        }
        return true;
    }

    /**
     * 获取token绑定的数据
     * @param string $key
     * @param string $token
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get_data(string $key = '', string $token = '')
    {
        $token = $token?:$this->get_token();
        $data = cache()->get($token);
        if(!$data){
            throw new TokenException('无数据',404);
        }
        if(is_string($data)){
            $data = json_decode($data,true);
        }
        return $key?$data[$key]:$data;
    }

}