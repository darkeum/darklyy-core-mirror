$(function(){
    let chat_id = $("#chat_id").val();
    let title = $("#chat_title").val();
    changeChat(chat_id, title);
});

CHAT.template_message = template_site;

function template_site(flag, elem){
    let arrData = elem.date.split(' ');
    let arrDataTime = arrData[1].split(':');
    let str = `
    <div data-id="${elem.id}" class="message ${flag ? 'message-right' : 'message-left'}">
        <div class="message_text">
            <div class="message_text-fio">${elem.fio}<div class="message_text-date">${arrDataTime[0]}:${arrDataTime[1]}</div></div>`;
            if(+elem.file === 0 || elem.file === undefined){
                str += `<div class="message_text-text">${elem.message}</div>`; 
            } else {
                str += `<div class="message_text-file">`;
                switch(elem.fileExtension){
                    default:
                        str += `<a target="_blank" href="/download/${elem.file}">${elem.fileName}</a>`;
                    break;
                    case "jpg":
                    case "jpeg":
                    case "bmp":
                    case "png":
                        str += `<a href="${elem.fileUrl}" class="message_fancybox"><img src="${elem.fileUrl}" class="message_image_img"></a>`;
                    break;
                    case "pdf":
                        str += `<a data-type="iframe" href="${elem.fileUrl}" class="message_fancybox message_iframe_a"></a>
                            <iframe scrolling="no" src="${elem.fileUrl}" frameborder="0" class="message_iframe"></iframe>`;
                    break;
                }
                str += `</div>`;
            }
           str +=`</div>
    </div>`;
    return str;
}