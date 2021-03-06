<?php

namespace App\Modules\Shop\Models;

use App\Modules\User\Model\AuthRecordModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\User\Model\SkillTagsModel;
use App\Modules\User\Model\TagsModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopModel extends Model
{
    protected $table = 'shop';
    //
    protected $fillable = [
        'id', 'uid', 'type', 'shop_pic', 'shop_name', 'shop_desc','province','city','status','created_at','updated_at','shop_bg',
        'seo_title','seo_keyword','seo_desc', 'is_recommend'
    ];

    public function employ_data()
    {
        return $this->hasMany('App\Modules\Employ\Models\EmployModel', 'employee_uid', 'uid')
            ->where('status', '=', '4');
    }
    /**
     * 根据用户id获取店铺详情
     * @author quanke
     * @param $uid  用户id
     * @return null
     */
    static function getShopInfoByUid($uid)
    {
        $shopInfo = ShopModel::where('uid',$uid)->first();
        if(!empty($shopInfo)){
            //查询该店铺是否设置技能
            $shopInfoTags = ShopTagsModel::where('shop_id',$shopInfo->id)->get()->toArray();
            if(!empty($shopInfoTags)){
                $tagIds = array();
                foreach($shopInfoTags as $key => $val){
                    $tagIds[] = $val['tag_id'];
                }
                //查询技能详情
                $tags = SkillTagsModel::whereIn('id',$tagIds)->get()->toArray();
                $shopInfo['tags'] = $tags;
            }
            return $shopInfo;
        }else{
            return false;
        }
    }


    /**
     * 根据店铺id获取店铺详情
     * @author quanke
     * @param $id  店铺id
     * @return null
     */
    static function getShopInfoById($id,$status = 0)
    {
        if($status){//对内店铺信息
            $shopInfo = ShopModel::where('shop.id',$id)
                ->leftJoin('users','users.id','=','shop.uid')
                ->select('shop.*','users.name')->first();
        }else{//对外店铺信息
            $shopInfo = ShopModel::where('shop.id',$id)->where('shop.status',1)
                ->leftJoin('users','users.id','=','shop.uid')
                ->select('shop.*','users.name')->first();
        }
        if(!empty($shopInfo)){
            //查询该店铺是否设置技能
            $shopInfoTags = ShopTagsModel::where('shop_id',$shopInfo->id)->get()->toArray();
            if(!empty($shopInfoTags)){
                $tagIds = array();
                foreach($shopInfoTags as $key => $val){
                    $tagIds[] = $val['tag_id'];
                }
                //查询技能详情
                $tags = SkillTagsModel::whereIn('id',$tagIds)->get()->toArray();
                $shopInfo['tags'] = $tags;
            }
            //查询地址
            if($shopInfo->province){
                $province = DistrictModel::where('id',$shopInfo->province)->select('id','name')->first();
                $shopInfo['province_name'] = $province->name;
            }else{
                $shopInfo['province_name'] = '';
            }
            if($shopInfo->city){
                $city = DistrictModel::where('id',$shopInfo->city)->select('id','name')->first();
                $shopInfo['city_name'] = $city->name;
            }else{
                $shopInfo['city_name'] = '';
            }
            return $shopInfo;
        }else{
            return false;
        }
    }


    /**
     * 保存店铺信息
     * @author quanke
     * @param $data
     * @return mixed
     */
    static function createShopInfo($data)
    {
        $status = DB::transaction(function () use ($data) {
            $arr = array(
                'uid'        => $data['uid'],
                'type'       => $data['type'],
                'shop_pic'   => $data['shop_pic'],
                'shop_name'  => $data['shop_name'],
                'shop_desc'  => $data['shop_desc'],
                'province'   => $data['province'],
                'city'       => $data['city'],
                'status'     => 1,//默认开启店铺
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            );
            $result = self::create($arr);
            //存入店铺技能标签关联表
            if(!empty($data['tags'])){
                $tagId = explode(',',$data['tags']);
                foreach($tagId as $value){
                    $tagData = array(
                        'shop_id' => $result['id'],
                        'tag_id' => $value
                    );
                    ShopTagsModel::create($tagData);
                }
            }
            return true;
        });
        return $status;
    }

    /**
     * 修改店铺信息
     * @author quanke
     * @param $data
     * @return mixed
     */
    static function updateShopInfo($data)
    {
        $status = DB::transaction(function () use ($data) {
            $arr = array(
                'shop_pic'   => $data['shop_pic'],
                'shop_name'  => $data['shop_name'],
                'shop_desc'  => $data['shop_desc'],
                'province'   => $data['province'],
                'city'       => $data['city'],
                'updated_at' => date('Y-m-d H:i:s'),
            );
            self::where('id',$data['id'])->update($arr);
            //查询店铺所有的标签id
            $oldTags = ShopTagsModel::shopTag($data['id']);
            $oldTags = array_flatten($oldTags);
            $oldTagsStr = implode(',',$oldTags);
            if($data['tags'] != $oldTagsStr)
            {
                //存入店铺技能标签关联表
                if(!empty($data['tags'])){
                    //删除原有标签
                    ShopTagsModel::where('shop_id',$data['id'])->delete();
                    $tagId = explode(',',$data['tags']);
                    foreach($tagId as $value){
                        $tagData = array(
                            'shop_id' => $data['id'],
                            'tag_id' => $value
                        );
                        ShopTagsModel::create($tagData);
                    }
                }
            }
            return true;
        });
        return $status;
    }


    /**
     * 批量开启店铺
     * @author quanke
     * @param $idArr
     * @return bool
     */
    static function AllShopOpen($idArr)
    {
        //查询批量操作的id数组是否关闭
        $res = ShopModel::whereIn('id',$idArr)->get()->toArray();
        if(!empty($res) && is_array($res)){
            $id = array();
            foreach($res as $k => $v){
                if($v['status'] == 2){
                    $id[] = $v['id'];
                }
            }
        }else{
            $id = array();
        }
        $status = ShopModel::whereIn('id',$id)->update(array('status' => 1));

        return is_null($status) ? true : $status;
    }

    /**
     * 批量关闭店铺(同时取消店铺推荐)
     * @author quanke
     * @param $idArr
     * @return bool
     */
    static function AllShopClose($idArr)
    {
        //查询批量操作的id数组是否关闭
        $res = ShopModel::whereIn('id',$idArr)->get()->toArray();
        if(!empty($res) && is_array($res)){
            $id = array();
            foreach($res as $k => $v){
                if($v['status'] == 1){
                    $id[] = $v['id'];
                }
            }
        }else{
            $id = array();
        }
        $status = ShopModel::whereIn('id',$id)->update(array('status' => 2,'is_recommend' => 0));

        return is_null($status) ? true : $status;
    }

    /**
     * 判断用户是否开启店铺
     * @author quanke
     * @param $uid 用户id
     * @return int
     */
    static function isOpenShop($uid)
    {
        $shopInfo = ShopModel::where('uid',$uid)->first();
        if(!empty($shopInfo)){
            $isOpenShop = $shopInfo->status;
        }else{
            $isOpenShop = 3;
        }
        return $isOpenShop;
    }



    /**
     * 根据店铺uid获取城市信息
     * @param $uid
     * @return string
     */
    static function getCityByUid($uid){
        $city = ShopModel::join('district', 'shop.city', '=', 'district.id')
            ->select('district.name')->where('shop.uid', $uid)->first();
        $city = $city ? $city->name : '';
        return $city;
    }

    /**
     * 根据用户id获取店铺id
     * @author quanke
     * @param $uid
     * @return string
     */
    static function getShopIdByUid($uid)
    {
        $shopInfo = ShopModel::where('uid',$uid)->first();
        if(!empty($shopInfo)){
            $shopId = $shopInfo->id;
        }else{
            $shopId = '';
        }
        return $shopId;
    }


    /**
     * 根据店铺id获取店铺列表
     * @param $shopIds 店铺id数组
     * @param array $merge 筛选条件
     * @return mixed
     */
    static function getShopListByShopIds($shopIds,$merge=array())
    {
        $shopList = ShopModel::whereRaw('1 = 1');
        if(isset($merge['shop_name'])){
            $shopList = $shopList->where('shop.shop_name','like','%'.$merge['shop_name'].'%');
        }
        $shopList = $shopList->whereIn('shop.id',$shopIds)
            ->with('employ_data')
            ->join('shop_focus','shop_focus.shop_id','=','shop.id')
            ->leftJoin('users','users.id','=','shop.uid')
            ->select('shop.*','users.email_status')
            ->orderby('shop_focus.created_at','DESC')
            ->groupBy('shop.id')
            ->paginate(10);
        if(!empty($shopList->toArray()['data'])){
            $userIds = array();
            $provinceId = array();
            $cityId = array();
            foreach($shopList as $k => $v){
                $userIds[] = $v['uid'];
                $provinceId[] = $v['province'];
                $cityId[] = $v['city'];
                //计算店铺好评率
                if(!empty($v['total_comment'])){
                    $v['comment_rate'] = intval($v['good_comment']/$v['total_comment'])*100;
                }else{
                    $v['comment_rate'] = 100;
                }
            }
            if(!empty($userIds)){
                $userAuthOne = AuthRecordModel::whereIn('uid', $userIds)->where('status', 2)
                    ->where('auth_code','!=','realname')->get()->toArray();
                $userAuthTwo = AuthRecordModel::whereIn('uid', $userIds)->where('status', 1)
                    ->whereIn('auth_code',['realname','enterprise'])->get()->toArray();
                $userAuth = array_merge($userAuthOne,$userAuthTwo);
                if(!empty($userAuth)){
                    //根据uid重组数组
                    $auth = array_reduce($userAuth,function(&$auth,$v){
                        $auth[$v['uid']][] = $v['auth_code'];
                        return $auth;
                    });
                }
                if(!empty($auth) && is_array($auth)){
                    foreach($auth as $e => $f){
                        $auth[$e]['uid'] = $e;
                        if(in_array('realname',$f)){
                            $auth[$e]['realname'] = true;
                        }else{
                            $auth[$e]['realname'] = false;
                        }
                        if(in_array('bank',$f)){
                            $auth[$e]['bank'] = true;
                        }else{
                            $auth[$e]['bank'] = false;
                        }
                        if(in_array('alipay',$f)){
                            $auth[$e]['alipay'] = true;
                        }else{
                            $auth[$e]['alipay'] = false;
                        }
                        if(in_array('enterprise',$f)){
                            $auth[$e]['enterprise'] = true;
                        }else{
                            $auth[$e]['enterprise'] = false;
                        }
                    }
                    foreach ($shopList as $key => $item) {
                        //拼接认证信息
                        foreach ($auth as $a => $b) {
                            if ($item->uid == $b['uid']) {
                                $shopList[$key]['auth'] = $b;
                            }
                        }
                    }
                }
            }
            //查询地区一级信息
            if(!empty($provinceId)){
                $provinceArr = DistrictModel::whereIn('id',$provinceId)->get()->toArray();
                if(!empty($provinceArr)){
                    foreach ($shopList as $key => $item) {
                        //拼接认证信息
                        foreach ($provinceArr as $a => $b) {
                            if ($item->province == $b['id']) {
                                $shopList[$key]['province_name'] = $b['name'];
                            }
                        }
                    }
                }
            }
            //查询地区二级信息
            if(!empty($cityId)){
                $cityArr = DistrictModel::whereIn('id',$cityId)->get()->toArray();
                if(!empty($cityArr)){
                    foreach ($shopList as $key => $item) {
                        //拼接认证信息
                        foreach ($cityArr as $a => $b) {
                            if ($item->city == $b['id']) {
                                $shopList[$key]['city_name'] = $b['name'];
                            }
                        }
                    }
                }
            }


            //查询店铺标签标签
            $arrSkill = ShopTagsModel::shopTag($shopIds);
            if(!empty($arrSkill) && is_array($arrSkill)){
                $arrTagId = array();
                foreach ($arrSkill as $item){
                    $arrTagId[] = $item['tag_id'];
                }
                if(!empty($arrTagId)){
                    $arrTagName = TagsModel::select('id', 'tag_name')->whereIn('id', $arrTagId)->get()->toArray();
                    $arrUserTag = array();
                    foreach ($arrSkill as $item){
                        foreach ($arrTagName as $value){
                            if ($item['tag_id'] == $value['id']){
                                $arrUserTag[$item['shop_id']][] = $value['tag_name'];
                            }
                        }
                    }
                    if(!empty($arrUserTag)){
                        foreach ($shopList as $key => $item){
                            foreach ($arrUserTag as $k => $v){
                                if ($item->id == $k){
                                    $shopList[$key]['skill'] = $v;
                                }
                            }
                        }
                    }
                }
            }

        }
        return $shopList;
    }


}

