<?php
    
function modalConstract($id, $fromid, $toid, $type, $status = false) 
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$modal = '';
    switch ($type){
    case 1:
    //ok
    $modal .= '  <div id="modal-ok-'.$id.'" class="popup-basic admin-form mfp-with-anim mfp-hide">
					<div class="panel">
					  <div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>'.$lang['task_comment_okf'].'</span></div>			
					  <form name="ok'.$id.'"  method="post" action="/'.ADMIN.'/task/addcomment/2">
									<div class="panel-body p25">                
									  <div class="section">
										<label for="comment" class="field prepend-icon">
										  <textarea id="comment" name="comment" placeholder="Комментарий к выполнению" class="gui-textarea"></textarea>
										  <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer"><strong>Hey You:</strong> We expect a great comment...</span>
										</label>
									  </div>
									  <div class="section">
										<label class="field prepend-icon append-button file"><span class="button">Выбрать</span>
											<input id="file1" type="file" name="file1" onchange="document.getElementById(\'uploader1\').value = this.value;" class="gui-file">
											<input id="uploader1" type="text" placeholder="Прикрепите файл при необходимости" class="gui-input">
											<label class="field-icon"><i class="fa fa-upload"></i></label>
										</label>
									  </div>
									<input type="hidden" name="id" value="'.$id.'">
									<input type="hidden" name="fromid" value="'.$fromid.'">
									<input type="hidden" name="toid" value="'.$toid.'">
								</div>
								<div class="panel-footer">
								  <button type="submit" class="button btn-success">Выполнить</button>
								</div>
							  </form>	
							</div>
						</div>';
    break;
    case 2:
    //comment
    $modal .= '  <div id="modal-comment-'.$id.'" class="popup-basic admin-form mfp-with-anim mfp-hide">
					<div class="panel">
					  <div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>'.$lang['task_comment_add'].'</span></div>			
					  <form name="comment'.$id.'"  method="post" action="/'.ADMIN.'/task/addcomment/1">
									<div class="panel-body p25">                
									  <div class="section">
										<label for="comment" class="field prepend-icon">
										  <textarea id="comment" name="comment" placeholder="Комментарий к выполнению" class="gui-textarea"></textarea>
										  <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer"><strong>Hey You:</strong> We expect a great comment...</span>
										</label>
									  </div>
									  <div class="section">
										<label class="field prepend-icon append-button file"><span class="button">Выбрать</span>
											<input id="file1" type="file" name="file1" onchange="document.getElementById(\'uploader1\').value = this.value;" class="gui-file">
											<input id="uploader1" type="text" placeholder="Прикрепите файл при необходимости" class="gui-input">
											<label class="field-icon"><i class="fa fa-upload"></i></label>
										</label>
									  </div>
									<input type="hidden" name="id" value="'.$id.'">
									<input type="hidden" name="fromid" value="'.$fromid.'">
									<input type="hidden" name="toid" value="'.$toid.'">
								</div>
								<div class="panel-footer">
								  <button type="submit" class="button btn-success">'.$lang['task_comment_add'].'</button>
								</div>
							  </form>	
							</div>
						</div>';
    break;
    case 3:
		//archive
    $modal .= '  <div id="modal-archive-'.$id.'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">';
						  if ($status != 1)
						  {
							  $modal .= '
							  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
							  <form method="post" action="/'.ADMIN.'/task/addarchive/1">
						  <div class="panel-body">
							<h3 class="mt5">Отправить задачу в архив?</h3>
							<hr class="short alt">
							<p>' . str_replace('[task]', $id, 'Вы действительно хотите отправить задачу [task] в архив?') .  '</p></div>
						  <div class="panel-footer text-right">
						  <input type="hidden" name="id" value="'.$id.'">
									<input type="hidden" name="status" value="'.$status.'">
							<button type="submit" class="btn btn-danger">В архив</button>
						  </div>
						  </form>';
						  }
						  else
						  {
							 $modal .= '<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['info'].'</span></div>
						  <div class="panel-body">К сожалению в архив можно перенести только выполненные или отмененные задачи.</div>
						  ';
						  }
						
						$modal .= '
						</div>
						</div>';
    break;
    case 4:
    //redirection 
    $modal .= '<div id="modal-redirection-'.$id.'" class="popup-basic admin-form mfp-with-anim mfp-hide">
					<div class="panel">
					  <div class="panel-heading"><span class="panel-title"><i class="fa fa-rocket"></i>Пере направление задачи</span></div>			
					  <form method="post" action="/'.ADMIN.'/task/redirection">
									<div class="panel-body p25"> 
                                    <div class="section">
                                    <select id="redirection" name="redirection">';
                                    $myq = $db->query("SELECT * FROM ".DB_PREFIX."_users");
                                    while($users = $db->getRow($myq)){
                                    if($users['surname'] == '') continue;
                                    $result_row = $users['surname'].' ';
                                    $result_row .= mb_substr($users['name'], 0, 1) . '. ';
                                    $result_row .= mb_substr($users['ochestvo'], 0, 1) . '.';
                                        $modal .= '<option value='.$users['id'].'>'.$result_row.'</option>';
                                    }
                                     $modal .= '</select>
									  </div>
									  <div class="section">
										<label for="comment" class="field prepend-icon">
										  <textarea id="comment" name="comment" placeholder="Комментарий" class="gui-textarea"></textarea>
										  <label for="comment" class="field-icon"><i class="fa fa-comments"></i></label><span class="input-footer"><strong>Hey You:</strong> We expect a great comment...</span>
										</label>
									  </div>
									  <div class="section">
										<label class="field prepend-icon append-button file"><span class="button">Выбрать</span>
											<input id="file1" type="file" name="file1" onchange="document.getElementById(\'uploader1\').value = this.value;" class="gui-file">
											<input id="uploader1" type="text" placeholder="Прикрепите файл при необходимости" class="gui-input">
											<label class="field-icon"><i class="fa fa-upload"></i></label>
										</label>
									  </div>
									<input type="hidden" name="id" value="'.$id.'">
									<input type="hidden" name="fromid" value="'.$fromid.'">
									<input type="hidden" name="toid" value="'.$toid.'">
								</div>
								<div class="panel-footer">
								  <button type="submit" class="button btn-success">Перенаправить</button>
								</div>
							  </form>	
							</div>
						</div>
                     </div>';
    break;
    }
	return $modal;	
}