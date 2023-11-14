<?php

function prohibited(){
    global $adminTpl;
    $adminTpl->admin_head('Счета | Запрет доступа');
    ?>
        <div id="content" class="animated fadeIn">	
			<div class="panel">								
				<div class="panel-body">
                    <h3>Вы не являетесь членом данной организации!</h3>
                </div>
            </div>
        </div>
    <?php
    $adminTpl->admin_foot();
}