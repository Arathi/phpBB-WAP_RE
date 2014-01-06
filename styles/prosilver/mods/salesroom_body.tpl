<div class="catSides"><a href="{U_INDEX}">{L_INDEX}</a>&gt;拍卖行(内测)</div>
<div class="nav"><b>小提示：</b>拍卖行插件仍在测试中，如有故障请联系Arathi哦！</div>

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

<!-- BEGIN goods_info -->
    <div class="{goods_info.ROW_CLASS}">
		<b>编号：</b>No.{goods_info.NUMBER}<br/>
        <b>名称：</b>{goods_info.GOODS_NAME}<br/>
        <b>拍卖开始时间：</b>{goods_info.START_TIME}<br/>
        <b>拍卖结束时间：</b>{goods_info.END_TIME}<br/>
        <b>当前价格：</b>{goods_info.GOODS_PRICE_NOW}<br/>
        <b>最小加价：</b>{goods_info.STEP_MONEY}<br/>
        <b>一口价：</b>{goods_info.GOODS_PRICE_MAX}<br/>
        <b>描述：</b><br/>{goods_info.GOODS_DESC}<br/>
	</div>
<!-- END goods_info -->

{PAGINATION}
