<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class TokenBucketController extends Controller
{
    // 令牌桶Key的名称
    private $keyName;
    // 最大令牌数
    private $maxNum;


    /**
     * 获取令牌桶的信息
     * TokenBucketsController constructor.
     */
    public function __construct()
    {
        $this->keyName = "TokenBucket";
        $this->maxNum  = 10000;
    }


    /**
     * 添加令牌
     * @param Int $num 加入的令牌数量
     * @return int
     */
    public function add(Request $request)
    {
        $num = $request->post("num");
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
            return json_encode(["status" => 1, "msg" => $num]);
        }
        return json_encode(["status" => 0, "msg" => $num]);
    }


    /**
     * 获取当前令牌的数量
     * @return int
     */
    public function getNums()
    {
        $otherNums = intval(Redis::lLen($this->keyName));
        return json_encode(["status" => 1, "msg" => $otherNums]);
    }


    /**
     * 获取令牌
     * @return Boolean
     */
    public function get()
    {
        $bool = Redis::rPop($this->keyName) ? true : false;
        if ($bool) {
            return json_encode(["status" => 1, "msg" => $bool]);
        } else {
            return json_encode(["status" => 0, "msg" => $bool]);
        }
    }


    /**
     * 重置令牌桶
     */
    public function reset()
    {
        Redis::del($this->keyName);
        $token = array_fill(0, $this->maxNum, 1);
        Redis::lPush($this->keyName, ...$token);
    }
}
