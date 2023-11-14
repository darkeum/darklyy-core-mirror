<div id="wrapper" class="profile">
	<div class="container">
		<div class="row">   							
			<div class="profile-left wow fadeInDown">
				{%TPL:profile.menu%}   
				<div class="chat">
							<input type="hidden" value="{%CHAT_ID%}" id="chat_id">
							<input type="hidden" value="{%CHAT_TITLE%}" id="chat_title">
							<input type="hidden" value="{%UID%}" id="chat_uid">
							<input type="hidden" value="{%UID_FIO%}" id="chat_fio">
							<input type="hidden" value="0" id="chat_page">
						<div class="chat-header">ЧАТ</div>
						<div class="chat-div" id="chat_body-message" onscroll="load_before_message(this);">
							<div data-id="336" class="message message-left">
								<div class="message_text">
									<div class="message_text-fio">Алексей<div class="message_text-date"></div></div><div class="message_text-text">Здравствуйте! Я могу Вам помочь?</div></div>
							</div>
						</div>
						<div class="chat-div-text">
							<div><label for="file"><input type="file" id="file" multiple onchange="load_file(this);"><i class="fa fa-plus-circle before_message" id="add_file"></i></label></div>
							<div><textarea id="chat_footer-input"></textarea></div>
							<div><i class="fa fa-paper-plane" aria-hidden="true" id="send_message" onclick="addMessage($('#chat_footer-input').val());"></i></div>
						</div>

					</div>
			</div>
			<div class="profile-content">
				<h1 class="user-heading-h1">{%TITLE%}</h1>
				<div class="user-razmestit-zacaz">
					<div class="user-razmestit-zacaz-div"><a href="#"><i class="fa fa-exchange" aria-hidden="true"></i> Разместить заказ</a><i onclick="serRazmestitZacaz()" class="fa fa-angle-down" aria-hidden="true"></i></div>
					<div class="user-razmestit-zacaz-hover">
						<a target="_blank" href="https://atri-energo.ru/sales/print/bill/{%ID%}&format=pdf&autoprint=true"><i class="fa fa-print" aria-hidden="true"></i> Распечатать счет</a>
						<a target="_blank" href="https://atri-energo.ru/sales/print/kp/{%ID%}&format=pdf&autoprint=true"><i class="fa fa-print" aria-hidden="true"></i> Распечатать КП</a>
						<a target="_blank" href="https://atri-energo.ru/catalog_v2/specification/sales/{%ID%}"><i class="fa fa-print" aria-hidden="true"></i> Распечатать спецификацию</a>
						<a href="javascript:void(0);" onclick="$('#doc_print_form').submit();"><i class="fa fa-print" aria-hidden="true"></i> Распечатать договор</a>
					</div>
				</div>
				<ul id="breadcrumbs-two" class="breadcrumb breadcrumbs-one breadcrumbs-one3">
					<li><a href="/" >[lang:main]</a><span class="go-page"></span></li>
					<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
					<li class="active" ><span>{%TITLE%}</span></li>
				</ul>

				<div class="blog-article-text">
					{%TEXT%}
				</div> 
			</div>
		</div>
	</div>
</div>
