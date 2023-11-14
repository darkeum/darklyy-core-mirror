<form method="POST" action="select/save_detail/{%ID%}">
 	
<div class="blog-post-wrapper">
                            <article class="blog-post-item">
                                <div class="inner-box">
                                    <div class="blog-post-content-desc">
                                        <div class="blog-post-content">
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0; width: 100%;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}

select {
    line-height: inherit;
    width: 100%;
    height: 27px;
}

input {
    width: 100%;
}
</style>
<table class="tg">
  <tr>
    <th class="tg-yw4l" colspan="2" rowspan="2">Размеры <br>помещения</th>
    <th class="tg-yw4l" colspan="2">Длина</th>
    <th class="tg-yw4l" colspan="2">Ширина</th>
    <th class="tg-yw4l" colspan="2">Высота</th>
    <th class="tg-yw4l" colspan="2">Наличие выступов, коллон</th>
    <th class="tg-yw4l" colspan="2">Высота здания</th>
  </tr>
  <tr>
    <td class="tg-yw4l" colspan="2"><input type="text" name="long" size="5" value="{%LONG%}"><br></td>
    <td class="tg-yw4l" colspan="2"><input type="text" name="width" size="5" value="{%WIDTH%}"><br></td>
    <td class="tg-yw4l" colspan="2"><input type="text" name="height" size="5" value="{%HEIGHT%}"><br></td>
	<td class="tg-yw4l" colspan="2"><input type="checkbox" name="vistup"  ><br></td>
    <td class="tg-yw4l" colspan="2"><input type="text" name="house_height" size="5" value="{%HOUSE_HEIGHT%}"><br></td>
  </tr>
  <tr>
    <td class="tg-yw4l" colspan="2"></td>
    <td class="tg-yw4l" colspan="2">Нарузка</td>
    <td class="tg-yw4l" colspan="4">Температура</td>
    <td class="tg-yw4l" colspan="4">Давление</td>
  </tr>
  <tr>
    <td class="tg-yw4l" colspan="2">Теплосеть</td>
    <td class="tg-yw4l"><span id="teplo_sum"></span></td>
    <td class="tg-yw4l">Гкал/ч</td>
    <td class="tg-yw4l">T1</td>
    <td class="tg-yw4l">
		<select name="t_1">
			{%T_1%}
		</select>
	</td>
    <td class="tg-yw4l">T2</td>
    <td class="tg-yw4l"><input type="text" name="t_2" size="5" value="{%T_2%}"></td>
    <td class="tg-yw4l">Р1, МПа</td>
    <td class="tg-yw4l"><input type="text" name="p_1" size="5" value="{%P_1%}"></td>
    <td class="tg-yw4l">Р2, МПа</td>
    <td class="tg-yw4l"><input type="text" name="p_2" size="5" value="{%P_2%}"></td>
  </tr>
  <tr>
    <td class="tg-yw4l" colspan="2">
		<select name="block_1">
			{%BLOCK_1%}
		</select>
	</td>
    <td class="tg-yw4l"><input type="text" class="sum" name="n_1" size="5" value="{%N_1%}" onchange="teplo_sum()"></td>
    <td class="tg-yw4l">Гкал/ч</td>
    <td class="tg-yw4l">T11</td>
    <td class="tg-yw4l"><input type="text" name="t_11" size="5" value="{%T_11%}"></td>
    <td class="tg-yw4l">T21</td>
    <td class="tg-yw4l"><input type="text" name="t_21" size="5" value="{%T_21%}"></td>
    <td class="tg-yw4l" colspan="2">Потери давления</td>
    <td class="tg-yw4l"><input type="text" name="pot_1" size="5" ></td>
    <td class="tg-yw4l">м.в.ст.</td>
  </tr>
  <tr>
     <td class="tg-yw4l" colspan="2">
		<select name="block_2">
			{%BLOCK_2%}
		</select>
	</td>
    <td class="tg-yw4l"><input type="text" class="sum" name="n_2" size="5" value="{%N_2%}" onchange="teplo_sum()"></td>
    <td class="tg-yw4l">Гкал/ч</td>
    <td class="tg-yw4l">T12</td>
    <td class="tg-yw4l"><input type="text" name="t_12" size="5" value="{%T_12%}"></td>
    <td class="tg-yw4l">T22</td>
    <td class="tg-yw4l"><input type="text" name="t_22" size="5" value="{%T_22%}"></td>
    <td class="tg-yw4l" colspan="2">Потери давления</td>
    <td class="tg-yw4l"><input type="text" name="pot_vent" size="5" ></td>
    <td class="tg-yw4l">м.в.ст.</td>
  </tr>
  <tr>
    <td class="tg-yw4l" colspan="2">
		<select name="block_3">
			{%BLOCK_3%}
		</select>
	</td>
    <td class="tg-yw4l"><input type="text" class="sum" name="n_3" size="5" value="{%N_3%}" onchange="teplo_sum()"></td>
    <td class="tg-yw4l">Гкал/ч</td>
    <td class="tg-yw4l">T13</td>
    <td class="tg-yw4l"><input type="text" name="t_13" size="5" value="{%T_13%}"></td>
    <td class="tg-yw4l">T23</td>
    <td class="tg-yw4l"><input type="text" name="t_23" size="5" value="{%T_23%}"></td>
    <td class="tg-yw4l" colspan="2">Потери давления</td>
    <td class="tg-yw4l"><input type="text" name="pot_ot_2" size="5" ></td>
    <td class="tg-yw4l">м.в.ст.</td>
  </tr>
  <tr>
    <td class="tg-yw4l" colspan="2">ГВС</td>
    <td class="tg-yw4l"><input type="text" class="sum" name="n_gvs" size="5" value="{%N_GVS%}" onchange="teplo_sum()"></td>
    <td class="tg-yw4l">Гкал/ч</td>
    <td class="tg-yw4l">T3</td>
    <td class="tg-yw4l"><input type="text" name="t_3" size="5" value="{%T_3%}"></td>
    <td class="tg-yw4l">B1</td>
    <td class="tg-yw4l"><input type="text" name="b_1" size="5" value="{%B_1%}"></td>
    <td class="tg-yw4l" colspan="2">Давление ХВС</td>
    <td class="tg-yw4l"><input type="text" name="dav_xvs" size="5" ></td>
    <td class="tg-yw4l">м.в.ст.</td>
  </tr>
</table>
<script type="text/javascript" >
function teplo_sum() {
var sum = 0;
$("input.sum").each(function(){
  sum += parseFloat($(this).val().trim().replace(/,/,'.'))||0;  
});

 $('#teplo_sum').html(sum.toFixed(2));
}
teplo_sum();
</script>
<br>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}
</style>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок ввода БВ</th>
    <th class="tg-031e" colspan="2">Узел учёта</th>
    <th class="tg-yw4l" colspan="2">Подпитка</th>
    <th class="tg-yw4l" colspan="3">Регулятор давления</th>
    <th class="tg-yw4l" colspan="3">Ограничитель расхода</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bv" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bv_uzel">
			{%BLOCK_BV_UZEL%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bv_podpit">
			{%BLOCK_BV_PODPIT%}
		</select>
	</td>
    <td class="tg-yw4l">
		<select name="block_bv_reg">
			{%BLOCK_BV_REG%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bv_reg_d">
			{%BLOCK_BV_REG_D%}
		</select>
	</td>
    <td class="tg-yw4l">
		<select name="block_bv_ogr">
			{%BLOCK_BV_OGR%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bv_ogr_d">
			{%BLOCK_BV_OGR_D%}
		</select>
	</td>
  </tr>
</table>
<br>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;}
.tg .tg-yw4l{vertical-align:top}
</style>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок отоп./ вент. БОВ-1</th>
    <th class="tg-031e" colspan="2">Схема</th>
    <th class="tg-yw4l" colspan="2">Насос</th>
    <th class="tg-yw4l" colspan="2">Клапан регулир-ий</th>
    <th class="tg-yw4l" colspan="2">Контроллер</th>
    <th class="tg-yw4l" colspan="2">Шкаф авт-ки</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bov_1" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bov_1_schema">
			{%BLOCK_BOV_1_SCHEMA%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_1_pump">
			{%BLOCK_BOV_1_PUMP%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_1_klapan">
			{%BLOCK_BOV_1_KLAPAN%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_1_control">
			{%BLOCK_BOV_1_CONTROL%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_1_auto">
			{%BLOCK_BOV_1_AUTO%}
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок отоп./ вент. БОВ-2</th>
    <th class="tg-031e" colspan="2">Схема</th>
    <th class="tg-yw4l" colspan="2">Насос</th>
    <th class="tg-yw4l" colspan="2">Клапан регулир-ий</th>
    <th class="tg-yw4l" colspan="2">Контроллер</th>
    <th class="tg-yw4l" colspan="2">Шкаф авт-ки</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bov_2" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bov_2_schema">
			{%BLOCK_BOV_2_SCHEMA%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_2_pump">
			{%BLOCK_BOV_2_PUMP%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_2_klapan">
			{%BLOCK_BOV_2_KLAPAN%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_2_control">
			{%BLOCK_BOV_2_CONTROL%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_2_auto">
			{%BLOCK_BOV_2_AUTO%}
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок отоп./ вент. БОВ-3</th>
    <th class="tg-031e" colspan="2">Схема</th>
    <th class="tg-yw4l" colspan="2">Насос</th>
    <th class="tg-yw4l" colspan="2">Клапан регулир-ий</th>
    <th class="tg-yw4l" colspan="2">Контроллер</th>
    <th class="tg-yw4l" colspan="2">Шкаф авт-ки</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bov_3" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bov_3_schema">
			{%BLOCK_BOV_2_SCHEMA%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_3_pump">
			{%BLOCK_BOV_3_PUMP%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_3_klapan">
			{%BLOCK_BOV_3_KLAPAN%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_3_control">
			{%BLOCK_BOV_3_CONTROL%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bov_3_auto">
			{%BLOCK_BOV_3_AUTO%}
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок ГВС БГВ</th>
    <th class="tg-031e" colspan="2">Схема</th>
    <th class="tg-yw4l" colspan="2">Насос</th>
    <th class="tg-yw4l" colspan="2">Клапан регулир-ий</th>
    <th class="tg-yw4l" colspan="2">Контроллер</th>
    <th class="tg-yw4l" colspan="2">Шкаф авт-ки</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bgv" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bgv_schema">
			{%BLOCK_BGV_SCHEMA%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bgv_pump">
			{%BLOCK_BGV_PUMP%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bgv_klapan">
			{%BLOCK_BGV_KLAPAN%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bgv_control">
			{%BLOCK_BGV_CONTROL%}
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bgv_auto">
			{%BLOCK_BGV_AUTO%}
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок подпитки БП-1</th>
    <th class="tg-031e" colspan="2">Наличие блока</th>
    <th class="tg-yw4l" colspan="2">Насосная станция</th>
    <th class="tg-yw4l" colspan="2">Повысит. насос</th>
    <th class="tg-yw4l" colspan="2">Клапан солиноид.</th>
    <th class="tg-yw4l" colspan="2">Расш. Бак</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bp_1" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bp_1_block">
			<option value="1">Автоматически</option>
			<option value="2">Принудит. Да</option>
			<option value="2">Принудит. Нет</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_1_spump">
			<option value="1">Автоматически</option>
			<option value="2">Принудит. Да</option>
			<option value="2">Принудит. Нет</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_1_pump">
			<option value="1">MHIL</option>
			<option value="2">MHI</option>
			<option value="3">Helix</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_1_klapan">
			<option value="1">Tork</option>
			<option value="2">Danfoss</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_1_bak">
			<option value="1">Wester</option>
			<option value="2">Flamco</option>
			<option value="3">Reflex</option>
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок подпитки БП-2</th>
    <th class="tg-031e" colspan="2">Наличие блока</th>
    <th class="tg-yw4l" colspan="2">Насосная станция</th>
    <th class="tg-yw4l" colspan="2">Повысит. насос</th>
    <th class="tg-yw4l" colspan="2">Клапан солиноид.</th>
    <th class="tg-yw4l" colspan="2">Расш. Бак</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bp_2" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bp_2_block">
		<option selected></option>
			<option value="1">Автоматически</option>
			<option value="2">Принудит. Да</option>
			<option value="2">Принудит. Нет</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_2_spump">
		<option selected></option>
			<option value="1">Автоматически</option>
			<option value="2">Принудит. Да</option>
			<option value="2">Принудит. Нет</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_2_pump">
		<option selected></option>
			<option value="1">MHIL</option>
			<option value="2">MHI</option>
			<option value="3">Helix</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_2_klapan">
		<option selected></option>
			<option value="1">Tork</option>
			<option value="2">Danfoss</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_2_bak">
		<option selected></option>
			<option value="1">Wester</option>
			<option value="2">Flamco</option>
			<option value="3">Reflex</option>
		</select>
	</td>
  </tr>
</table>
<br>
<table class="tg">
  <tr>
    <th class="tg-031e" colspan="2">Блок подпитки БП-3</th>
    <th class="tg-031e" colspan="2">Наличие блока</th>
    <th class="tg-yw4l" colspan="2">Насосная станция</th>
    <th class="tg-yw4l" colspan="2">Повысит. насос</th>
    <th class="tg-yw4l" colspan="2">Клапан солиноид.</th>
    <th class="tg-yw4l" colspan="2">Расш. Бак</th>
  </tr>
  <tr>
    <td class="tg-031e" colspan="2"><input type="checkbox" name="block_bp_3" size="5" ></td>
    <td class="tg-031e" colspan="2">
		<select name="block_bp_3_block">
		<option selected></option>
			<option value="1">Автоматически</option>
			<option value="2">Принудит. Да</option>
			<option value="2">Принудит. Нет</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_3_spump">
		<option selected></option>
			<option value="1">Автоматически</option>
			<option value="2">Принудит. Да</option>
			<option value="2">Принудит. Нет</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_3_pump">
		<option selected></option>
			<option value="1">MHIL</option>
			<option value="2">MHI</option>
			<option value="3">Helix</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_3_klapan">
		<option selected></option>
			<option value="1">Tork</option>
			<option value="2">Danfoss</option>
		</select>
	</td>
    <td class="tg-yw4l" colspan="2">
		<select name="block_bp_3_bak">
		<option selected></option>
			<option value="1">Wester</option>
			<option value="2">Flamco</option>
			<option value="3">Reflex</option>
		</select>
	</td>
  </tr>
</table>
<div style="display: none;">
<input type="text" name="bov1" size="40" placeholder="Нагрузка"/><br>
		<input type="text" name="t1" size="40" placeholder="Температура подачи"><br>
		<input type="text" name="t2" size="40" placeholder="Температура обратки"><br>
		<br>
		<input type="text" name="t11" size="40" placeholder="Температура подачи вторичный контур"><br>
		<input type="text" name="t21" size="40" placeholder="Температура обратки вторичный контур"><br>				
		<br>
		<input type="text" name="p1" size="40" placeholder="Давление подачи"><br>
		<input type="text" name="p2" size="40" placeholder="Давление обратки"><br>
	
		</div>
		<br>
		<center>
			<input name="submit" type="submit"  style="width: 300px" class="btn btn btn-success" id="sub" value="Выполнить" />
				</center>
	</form>
	
	
	
	
	
	
	
	
	
	
	
	
	      </div>


                                       
                                    </div>

                                  


                                </div>
                            </article>


                        </div>