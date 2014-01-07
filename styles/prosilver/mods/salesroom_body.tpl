<div class="catSides"><a href="{U_INDEX}">{L_INDEX}</a>&gt;拍卖行(内测)</div>
<div class="nav"><i><b>小提示：</b>拍卖行插件仍在测试中，如有故障请联系Arathi哦！</i></div>

<!-- BEGIN switch_no_goods -->
	<div class="row_hard"><b>没有正在拍卖的商品！</b></div>
<!-- END switch_no_goods -->

<!-- BEGIN switch_has_goods -->
	<div class="row_hard"><b>正在拍卖的商品如下：</b></div>
<!-- END switch_has_goods -->

<!-- BEGIN goods_rows -->
	<div class="{goods_rows.ROW_CLASS}">
		No.{goods_rows.NUMBER} <a href="{goods_rows.U_GOODS_INFO}">{goods_rows.GOODS_NAME}</a><br/>[结束时间：{goods_rows.END_TIME}]
	</div>
<!-- END goods_rows -->

<!-- BEGIN auctioneer_message -->
    {auctioneer_message.MESSAGE_TEXT}<br/>
<!-- END auctioneer_message -->

<!-- BEGIN message -->
    <div class="row_hard">{message.MESSAGE_TEXT}</div>
<!-- END message -->

<!-- BEGIN goods_info -->
    <div class="{goods_info.ROW_CLASS}">
        <b>商品编号：</b>No.{goods_info.NUMBER}<br/>
        <b>商品名称：</b>{goods_info.GOODS_NAME}<br/>
        <b>拍卖开始时间：</b>{goods_info.START_TIME}<br/>
        <b>拍卖结束时间：</b>{goods_info.END_TIME}<br/>
        <b>当前价格：</b>{goods_info.GOODS_PRICE_NOW}<br/>
        <b>最小加价：</b>{goods_info.STEP_MONEY}<br/>
        <b>一口价：</b>{goods_info.GOODS_PRICE_MAX}<br/>
        <b>描述：</b><br/>{goods_info.GOODS_DESC}<br/>
    </div>
<!-- END goods_info -->

<!-- BEGIN goods_bid -->
    <form action='goodsinfo.php?gid={goods_bid.NUMBER}&action=bid' method='post'>
        <div class="row_hard">您当前有：{goods_bid.MONEY_NOW}红豆</div>
        <input type='text' name='bid_money' value='0' />
        <input type='submit' name='submit' value='出价' />
    </form>
<!-- END goods_bid -->

<!-- BEGIN bid_log_head -->
    <br/><b>出价记录：</b>
<!-- END bid_log_head -->
<!-- BEGIN bid_log -->
    <div class="{bid_log.ROW_CLASS}">
        <b>单号：</b>{bid_log.BID_ID}<br/>
        <b>买家：</b>{bid_log.U_AUCTIONEER}<br/>
        <b>时间：</b>{bid_log.BID_TIME}<br/>
        <b>出价：</b>{bid_log.BID_MONEY}<br/>
    </div>
<!-- END bid_log -->

<!-- BEGIN switch_no_signed -->
	<div class="row_hard"><b>没有现在没有可用的拍卖牌号！</b></div>
<!-- END switch_no_signed -->

<!-- BEGIN switch_has_signed -->
	<b>您拥有的拍卖牌号如下：</b>
<!-- END switch_has_signed -->

<!-- BEGIN auctioneer_info -->
    <div class="{auctioneer_info.ROW_CLASS}">
        No.{auctioneer_info.AUCTIONEER} {auctioneer_info.FAKENAME} 【<b>{auctioneer_info.U_SELECT}</b>】
    </div>
<!-- END auctioneer_info -->

<!-- BEGIN auctioneer_register -->
    <br/>
    <div class="row_easy">注册新的拍卖人号牌：</div>
    <form action='sign.php' method='post'>
        拍卖人假名：<input type='text' name='fakename' value='{auctioneer_register.DEFAULT_FAKENAME}' />
        <input type='submit' name='submit' value='注册' />
    </form>
<!-- END auctioneer_register -->

{PAGINATION}
