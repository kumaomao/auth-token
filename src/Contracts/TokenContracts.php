<?php
namespace Kumaomao\AuthToken\Contracts;

interface  TokenContracts
{

    public function get_token(string $token_name='token'):string;

    public function set_token(array $data,string $token = ''):string;

    public function ref_token(string $token = '',bool $is_old=false):string;

    public function del_token(string $token = ''):bool;

    public function check_token(string $token='');

    public function get_data(string $key='',string $token = '');



}