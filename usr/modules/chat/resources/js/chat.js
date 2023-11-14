window.onload = function(){
    const first_data = {
        list: [],
        message: null,
        messages: [],
        page_message: 1,
        page_list: 1,
        chat_data: {
            title: "Выберите чат",
        },
        reply_message_data : null,
        load_list_allow: true,
        load_messages_allow: true,
        load_list: false,
        load_messages: false,
    };
    const app = Vue.createApp({
        compilerOptions: {
            delimiters: ['@{', '}'],
            comments: true
        },
        data(){
            const data = {};
            for(key in first_data){
                data[ key ] = first_data[ key ];
            }
            data.uid = null;
            return data;
        },
        async mounted(){
            this.uid = document.getElementById("chat_uid").value;
            const token = document.getElementById("chat_token").value;
            const ws = new WebSocket(`wss://atri-energo.ru/websocket?token=${token}&wss_type=chat`);
            ws.onopen = function() {
                console.log("connection success");
            };
            ws.onmessage = function(e) {
                const data = JSON.parse(e.data);
                console.log(data)
                // const message = data[0];
                // if(this.chat_data?.id == message.chat_id){
                //     this.messages.push(message);
                // }

                // const json = JSON.parse(e.data);
                // const chat_id = get_chat_id();
            
                // is_up_chat_list(json[0]["chat_id"]);
                // is_update_last_message(json[0]);
            
                // if(chat_id === +json[0]["chat_id"]){
                //     append_message(template_message(false, json[0]));
                //     message_scroll_top();
                //     read_chat_message(chat_id);
                // } else {
                //     if('add_count_no_read_message' in CHAT){
                //         CHAT.add_count_no_read_message(json[0]["chat_id"]);
                //     }
                //     if('notif_onmessage' in CHAT){
                //         CHAT.notif_onmessage(json);
                //     }
                // }
                // is_update_all_no_read_message_count();
            };
            ws.onclose = function() {
                alert("connection close");
            };

            const func = async () => {
                for(key in first_data){
                    this[ key ] = first_data[ key ];
                }

                this.list = await this.getChatList();
            }
            
            document.getElementById("chat-modal").addEventListener('show.bs.modal', func);
        },
        methods: {
            async getChatList(){
                this.load_list = true;
                const list = await this.getChatList_();
                this.load_list = false;
                return list;
            },
            async getChatList_(){
                const list = await ( await fetch(`/administration/module/chat/list?page=${this.page_list}`) ).json();
                return list.data;
            },
            async open(item){
                this.chat_data = item;
                this.page_message = 1;
                this.load_messages_allow = true;
                const messages = await this.getChatMessages(item.id);
                this.messages = messages;

                this.$nextTick(() => {
                    const container = document.getElementById("chat-messages-items");
                    container.scrollTop = container.scrollHeight;
                });
            },
            async getChatMessages(id){
                this.load_messages = true;
                const messages = await this.getChatMessages_(id);
                this.load_messages = false;
                return messages;
            },
            async getChatMessages_(id){
                const last_id = this.messages[0]?.id ?? false;
                const last = last_id ? `&last_id=${last_id}` : ``;
                return await ( await fetch(`/administration/module/chat/messages/${id}?page=${this.page_message}${last}`) ).json();
            },
            parse_date(value){
                return moment(value).format('DD.MM.YYYY HH:mm');
            },
            async loadPrevMessages(event){
                if(event.target.scrollTop < 100 && this.load_messages_allow){
                    this.load_messages_allow = false;
                    this.page_message++;
                    const messages = await this.getChatMessages(this.chat_data.id);
                    event.target.scrollTop = 1;
                    this.messages.unshift(...messages);
                    this.load_messages_allow = messages.length > 0;
                }
            },
            async loadNextList(event){
                if(event.target.scrollTop >= (event.target.scrollHeight - event.target.offsetHeight - 100) && this.load_list_allow){
                    this.load_list_allow = false;
                    this.page_list++;
                    const list = await this.getChatList();
                    this.list.push(...list);
                    this.load_list_allow = list.length > 0;
                }
            },
            async sendMessage(){
                const form = new FormData;
                form.append("message", this.message);
                form.append("chat_id", this.chat_data.id);
                this.message = null;
                const new_message = await ( await fetch(`/administration/module/chat/message`, {
                    method: "POST",
                    body: form,
                }) ).json();
                this.messages.push(new_message);
            },
            getListIcon(item){
                switch(item.module){
                    default:
                        return `<i class="fas fa-bullhorn"></i>`;
                    case "boiler_bid":
                        return `<i class="fas fa-cauldron" title="Чат котла"></i>`;
                    case "sales":
                        if(item.type == 2){
                            return `<i class="fas fa-shopping-basket" title="Чат продажи с клиентом"></i>`;
                        } else {
                            return `<i class="fas fa-shopping-cart" title="Чат продажи для портала"></i>`;
                        }
                }
            },
            async loadFile(event){
                const form = new FormData();
                form.append("chat_id", this.chat_data.id);
                event.target.files.forEach(file => {
                    form.append("files[]", file);
                });
                const response = await ( await fetch(`/administration/module/chat/load_file`, {
                    method: "POST",
                    body: form,
                }) ).json();
            },
        },
    });
    
    app.mount("#chat_content");
}