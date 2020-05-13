<?php

namespace App\Http\Controllers\Algorithm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
class TokenBucketsController extends Controller
{
    // 令牌桶Key的名称
    private $keyName;
    // 最大令牌数
    private $maxNum;


    /**
     * 获取令牌桶的信息
     * TokenBucketsController constructor.
     */
    public function __construct($_keyName, $_maxNum)
    {
        $this->keyName = $_keyName;
        $this->maxNum  = $_maxNum;
    }


    /**
     * 添加令牌
     * @param Int $num 加入的令牌数量
     * @return int
     */
    public function add($num)
    {
        // 获取当前剩余令牌数
        $otherNum = intval(Redis::lLen($this->keyName));
        // 获取设置的最大令牌数
        $maxNum   = intval($this->maxNum);
        // 计算最大可加入的令牌数量，不能超过最大令牌数
        $num = $maxNum >= $otherNum + $num ? $num : $maxNum - $otherNum;
        // 加入令牌
        if ($num > 0) {
            $token = array_fill(0, $num, 1);
            Redis::lPush($this->keyName, ...$token);
            return $num;
        }
        return 0;
    }


    /**
     * 获取当前令牌的数量
     * @return int
     */
    public function getNums()
    {
        $otherNums = intval(Redis::lLen($this->keyName));
        return $otherNums;
    }


    /**
     * 获取令牌
     * @return Boolean
     */
    public function get()
    {
        return Redis::rPop($this->keyName) ? true : false;
    }


    /**
     * 重置令牌桶
     */
    public function reset()
    {
        Redis::del($this->keyName);
        $this->add($this->maxNum);
    }


    /**
     * 设置库存
     * @param String $key
     * @param Int    $value
     */
    public function setInventory($key, $value)
    {
        Redis::set($key, $value);
    }


    /**
     * 使用库存
     * @param String $key
     * @param Int    $value
     */
    public function useInventory($key, $value = 1)
    {
        Redis::set($key, $value);
        $res = Redis::decr($key, $value);
        return $res;
    }


    /**
     * 返还库存
     * @param String $key
     * @param Int    $value
     */
    public function addInventory($key, $value = 1)
    {
        Redis::incr($key, $value);
    }
}
