<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


function content_main() 
{
	global $adminTpl, $core, $db, $lang, $whereSM, $typeSM;
	$lnn = $lang['storage_count_order'];
	$adminTpl->admin_head($lang['storage_main'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'storage';
	$atributs['module_open'] = 'detail';
	$atributs['js'] = '  setTimeout(async function () {await setGridParam(); await reloadGrid();}, 300);';	
	loadGrid('jqStorageMain', $lnn, $atributs, ROOT.'/usr/modules/storage/admin/grid/');

	echo <<<HTML
		<script>
			function delModal(id){
				swal({
				  title: "Вы уверены?",
				  text: "Удалить заявку",
				  icon: "warning",
				  buttons: true,
				  dangerMode: true,
				})
				.then(async (willDelete) => {
				  if (willDelete) {
				    const lw = new LW;
					lw.on();
					await delete_storage(id);
					await reloadGrid();
					lw.off();
				  }
				});
			}

			async function reloadGrid(){
				await $('#jqStorageMain').trigger('reloadGrid');
			}

			async function delete_storage(id){
				return await (await fetch(`/administration/module/storage/delete_storage/\${id}`)).text();
			}

			async function setGridParam(){
				$("#jqStorageMain").jqGrid('setGridParam',{
					postData:{
						status: '1'
					}
				});
			}
		</script>
HTML;

	$adminTpl->admin_foot();
} 