<div id="wrapper" class="profile">
	<div class="container">
		<div class="row">   							
			<div class="profile-left wow fadeInDown">
			<div class="profile-menu">
				{%TPL:profile.menu%}  
			</div>
			
			[chat]
					<div class="chat">
						<input type="hidden" value="{%CHAT_ID%}" id="chat_id" />
						<input type="hidden" value="{%CHAT_TITLE%}" id="chat_title" />
						<input type="hidden" value="{%UID%}" id="chat_uid" />
						<input type="hidden" value="{%UID_FIO%}" id="chat_fio" />
						<input type="hidden" value="0" id="chat_page" />
						<div class="chat-header">ЧАТ</div>
						<div id="chat_body" onscroll="load_before_message(this);">
							<div id="chat_body-message" >
								<div data-id="336" class="message message-left">
									<div class="message_text">
										<div class="message_text-fio">Алексей<div class="message_text-date"></div></div><div class="message_text-text">Здравствуйте! Я могу Вам помочь?</div></div>
								</div>
							</div>
						</div>
						<div class="chat-div-text">
							<div><label for="file"><input type="file" id="file" multiple onchange="load_file(this);"><i class="fa fa-plus-circle before_message" id="add_file"></i></label></div>
							<div><textarea id="chat_footer-input"></textarea></div>
							<div><i class="fa fa-paper-plane" aria-hidden="true" id="send_message" onclick="addMessage($('#chat_footer-input').val());"></i></div>
						</div>
					</div>
				[/chat]
			</div>
			<div class="profile-content">
				{%INFO%}	
				{%CONTENT%}	 
			</div>
		</div>
	</div>
</div>
