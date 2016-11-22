<div class="g-main g-releasetask">
    <h4 class="text-size16 cor-blue2f u-title">我卖出的作品</h4>
    <div class="space-12"></div>
    <div class="clearfix g-reletaskhd hidden-xs">
        <form action="/user/acceptTasksList" method="get">
            <div class="pull-left">
                <div class="pull-left">
                    <select class="form-control" name="time">
                        <option value="0">全部状态</option>
                    </select>
                </div>
                <div class="pull-left">
                    <select class="form-control" name="status">
                        <option value="0">全部时间</option>
                    </select>
                </div>
                <button type="submit">
                    <i class="fa fa-search text-size16 cor-graybd"></i> 搜索
                </button>
            </div>
        </form>
    </div>
    <div class="space-6"></div>
    <ul id="useraccept">
            <li class="row width590">
                <div class="col-sm-1 col-xs-2 usercter">
                    <img src="" >
                </div>


                <div class="col-sm-11 col-xs-10 usernopd">
                    <div class="col-sm-9 col-xs-8">
                        <div class="text-size14 cor-gray51"><span class="cor-orange">￥1111</span>&nbsp;&nbsp;<a class="cor-blue42" href="">作品名称</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;待付款</div>
                        <div class="space-6"></div>
                        <p class="cor-gray87"><i class="fa fa-user cor-grayd2"></i> 水馒头&nbsp;&nbsp;&nbsp;<i class="fa fa-clock-o cor-grayd2"></i> 2017-06-02</p>
                        <div class="space-6"></div>
                        <p class="cor-gray51 p-space">想找一个熟悉microship芯片12F系列的朋友，在读懂程序功能的基础上，写出功能流程说明和注</p>
                        <div class="space-2"></div>
                        <div class="g-userlabel"><a href="">设计</a></div>
                    </div>
                    <div class="col-sm-3 col-xs-4 text-right hiden590">
                        <a class="btn-big bg-blue bor-radius2 hov-blue1b" target="_blank" href="">查看</a>
                    </div>
                    <div class="col-xs-12"><div class="g-userborbtm"></div></div>
                </div>
            </li>
        <li class="row width590">
            <div class="col-sm-1 col-xs-2 usercter">
                <img src="" >
            </div>


            <div class="col-sm-11 col-xs-10 usernopd">
                <div class="col-sm-9 col-xs-8">
                    <div class="text-size14 cor-gray51"><span class="cor-orange">￥1111</span>&nbsp;&nbsp;<a class="cor-blue42" href="">作品名称</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;交易成功</div>
                    <div class="space-6"></div>
                    <p class="cor-gray87"><i class="fa fa-user cor-grayd2"></i> 水馒头&nbsp;&nbsp;&nbsp;<i class="fa fa-clock-o cor-grayd2"></i> 2017-06-02</p>
                    <div class="space-6"></div>
                    <p class="cor-gray51 p-space">想找一个熟悉microship芯片12F系列的朋友，在读懂程序功能的基础上，写出功能流程说明和注</p>
                    <div class="space-2"></div>
                    <div class="g-userlabel"><a href="">设计</a></div>
                </div>
                <div class="col-sm-3 col-xs-4 text-right hiden590">
                    <a class="btn-big bg-blue bor-radius2 hov-blue1b" target="_blank" href="">查看</a>
                </div>
                <div class="col-xs-12"><div class="g-userborbtm"></div></div>
            </div>
        </li>


    </ul>
    {{--
        <div class="g-nomessage">暂无信息哦 ！</div>
    --}}
    <div class="space-20"></div>
    <div class="dataTables_paginate paging_bootstrap">
        <ul class="pagination">
                <li><a href="">&lt;</a></li>
            <li class="active"><a href="" class="bg-blue">1</a></li>
            <li class=""><a href="" class="bg-blue">2</a></li>
                <li><a href="">&gt;</a></li>
        </ul>
    </div>
</div>

{!! Theme::asset()->container('custom-css')->usepath()->add('messages','css/usercenter/messages/messages.css') !!}
{!! Theme::asset()->container('custom-css')->usepath()->add('usercenter','css/usercenter/usercenter.css') !!}
{!! Theme::asset()->container('custom-css')->usePath()->add('shop-css', 'css/usercenter/shop/shop.css') !!}
{!! Theme::asset()->container('custom-js')->usepath()->add('nopie','js/doc/nopie.js') !!}

