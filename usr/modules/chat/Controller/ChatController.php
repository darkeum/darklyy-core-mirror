<?php

namespace Chat\Controller;

use Boot\System\Http\Request;

use \Boot\Support\Facades\Auth;
use \Boot\Controller\AbstractController;

class ChatController extends AbstractController
{
	static function index()
	{
		display('@chat/manager/index.html.twig', [
			'modal' => static::modal(),
			'style' => static::style(),
		]);
	}

	static function create()
	{
	}

	static function store()
	{
	}

	static function show($id)
	{
	}

	static function edit($id)
	{
	}

	static function update()
	{
	}

	static function destroy($id)
	{
	}

	static function list()
	{
		$list = \Chat\Model\Chat::with("last_message")->whereHas("chat_user", function ($query) {
			$query->where("uid", Auth::id());
		})->orderByDesc("last_message_date")->paginate(30);

		echo $list->toJson();
	}

	static function messages(Request $request, $id)
	{
		$last_id = $request->query->last_id ?? false;
		$messages = \Chat\Model\ChatMessage::with("user")->where("chat_id", $id)->orderByDesc("id");
		if ($last_id) {
			$messages->where("id", "<", $last_id);
		}
		echo $messages->paginate(15)->reverse()->values()->toJson();
	}

	static function getToken()
	{
		$user_token = \Boot\Models\UsersToken::where("uid", Auth::id())->where("platform", "app")->where("status", 1)->firstOrFail();
		return $user_token->token;
	}

	static function script(){
		return "<script src=\"usr/modules/chat/resources/js/chat.js\"></script>";
	}

	static function style()
	{
		return "<link rel=\"stylesheet\" type=\"text/css\" href=\"/usr/modules/chat/resources/css/chat.css\">";
	}

	static function modal(){
		$uid = Auth::id();
		$token = static::getToken();
		return <<<HTML
			<div class="modal fade" id="chat-modal" aria-hidden="true">
			  <div class="modal-dialog modal-xl modal-fullscreen-xxl-down">
			    <div class="modal-content">
			      <div class="modal-body p-0">
					  
				  	<div class="card" id="chat_content">
						<input type="hidden" id="chat_uid" value="{$uid}">
						<input type="hidden" id="chat_token" value="{$token}">
						<div class="row g-0 p-0" style="height: inherit;">
            				<div class="col-12 col-lg-5 col-xl-4 border-end d-flex flex-column" style="height: inherit;">

            					<div class="px-4">
            						<div class="d-flex align-items-center">
            							<div class="flex-grow-1">
            								<input type="text" class="form-control my-3" placeholder="Поиск...">
            							</div>
            						</div>
            					</div>

								<div id="chat-list" @scroll="loadNextList">
									<a href="javascript:void(0)" class="chat-list-container list-group-item list-group-item-action border-0" 
									v-for="item in list" @click="open(item)">
            							<div class="badge bg-primary float-right" v-if="false">5</div>
            							<div class="d-flex align-items-start">
											<div class="me-2 rounded-circle chat-list-item" v-html="getListIcon(item)"></div>
            								<div class="flex-grow-1 ms-3" style="flex: 1">
												<div class="d-flex justify-content-between">
													<div>@{item.title}</div>
													<div class="small text-muted" v-if="item.last_message">@{parse_date(item.last_message.date)}</div>
												</div>
            									<div class="small text-muted text-truncate" style="width: 30vh" v-if="item.last_message">
													@{item.last_message.message}
												</div>
            								</div>
            							</div>
            						</a>
								</div>

            					<div class="m-3" v-show="list.length == 0 && !load_list">Чаты отсутствуют</div>
								<div v-if="load_list">
									<div class="d-flex justify-content-center py-3">
										<i class="fas fa-spinner text-muted fa-2x fa-spin"></i>
									</div>
								</div>
								<div v-else style="height: 56px"></div>

            					<hr class="d-block d-lg-none mt-1 mb-0">
            				</div>
            				<div class="col-12 col-lg-7 col-xl-8 d-flex flex-column" style="height: inherit;">
            					<div class="py-3 px-4 border-bottom">
            						<div class="d-flex align-items-center py-1 justify-content-between">
										<div class="flex-grow-1 pl-3 d-flex align-items-center">
            								<strong>@{chat_data.title}</strong>
											<div class="ms-3" v-show="load_messages">
												<i class="fas fa-spinner text-muted fa-lg fa-spin"></i>
											</div>
            							</div>
										<div><i class="fas fa-times cp" data-bs-dismiss="modal"></i></div>
            						</div>
            					</div>

            					<div class="p-4 bg-light" id="chat-messages-items" @scroll="loadPrevMessages">
            						<div :class="{
            							    'chat-message-right': message.uid == uid, 
            							    'chat-message-left': message.uid != uid, 
            							    'mb-4': message.uid == uid,
            							    'pb-4': message.uid != uid,
            							}" v-for="message in messages" :key="message.id" :id="'message-item-' + message.id" :ref="'message-item-' + message.id">
            							<div>
            								<img :src="message.avatar" class="rounded-circle me-1" width="40" height="40">
            							</div>
            							<div class="flex-shrink-1 bg-white rounded py-2 px-3 me-3">
            								<div class="fw-bold mb-1">
												<div v-if="message.uid == uid">Вы</div>
												<div v-else>@{message.user.short_user_name}</div>
											</div>
											<div class="text-break">@{message.message}</div>
											<div class="d-flex align-items-end">
												<div class="text-muted small text-nowrap mt-2">@{parse_date(message.date)}</div>
												<i class="fas fa-reply ms-2 cp" title="Ответ" @click="reply_message_data = message"></i>
											</div>
            							</div>
            						</div>
            					</div>
									
            					<div v-if="reply_message_data" class="border-top d-flex justify-content-between align-items-center px-3 py-2 bg-light">
									<div>
										<div class="fw-bold">@{reply_message_data.user.short_user_name}</div>
        						    	<div>@{reply_message_data.message}</div>
									</div>
        						    <div><i class="fas fa-times cp" @click="reply_message_data = null"></i></div>
        						</div>
            					<div class="flex-grow-0 py-3 px-4 border-top" v-if="chat_data.id">
            						<div class="input-group">
										<label for="chat_file" class="btn btn-outline-secondary">
											<input type="file" class="d-none" id="chat_file" @change="loadFile" multiple>
											<i class="fas fa-plus"></i>
										</label>
										<button type="button" class="btn btn-outline-secondary"><i class="far fa-laugh"></i></button>
            							<input type="text" class="form-control" placeholder="Введите ваше сообщение" @keyup.enter="sendMessage" v-model="message">
            							<button type="button" class="btn btn-primary" @click="sendMessage">Отправить</button>
            						</div>
            					</div>
									
            				</div>
            			</div>
					</div>

				  </div>
			    </div>
			  </div>
			</div>
HTML;
	}
}
