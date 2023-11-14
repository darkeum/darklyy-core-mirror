/*
 * Developed by InterMate Group
 */
 
 var userMod = false;

function shareWindow(e) {
  var h = 500,
      w = 500;
  window.open(e, '', 'scrollbars=1,height='+Math.min(h, screen.availHeight)+',width='+Math.min(w, screen.availWidth)+',left='+Math.max(0, (screen.availWidth - w)/2)+',top='+Math.max(0, (screen.availHeight - h)/2));
}

function gid(elemid) 
{ 
	return document.getElementById(elemid); 
}

function isValidEmail (email)
{
	return (/^([a-z0-9_\-]+\.)*[a-z0-9_\-]+@([a-z0-9][a-z0-9\-]*[a-z0-9]\.)+[a-z]{2,4}$/i).test(email);
}

function showload() 
{

}

function setCookie(name, value, exp_time)
{
    var exp = new Date ();
    exp.setTime(exp.getTime() + (exp_time * 1000));
    document.cookie = name + "=" + escape(value) + "; expires=" + exp.toGMTString() + "; path=/;";
}

function getCookie(name)
{
    var name = name;
    var arg = name + '=';
    var alen = arg.length;
    var clen = document.cookie.length;
    var i = 0;

    while (i < clen) {
        var j = i + alen;

        if (document.cookie.substring(i, j) == arg)
        {
            var endstr = document.cookie.indexOf(';', j);

            if (endstr == -1)
            {
                endstr = document.cookie.length;
            }

            return unescape(document.cookie.substring(j, endstr));
        }

        i = document.cookie.indexOf(' ', i) + 1;

        if (i == 0)
        {
            break; 
        }
    }
    return false;
}

function switchBlock(id)
{
    var img = 'img_' + id;

    if (gid('block_' + id).style.display != 'none')
    {
		showhide('block_' + id);
		gid(img).src = 'media/other/close.png';
        setCookie('Block_' + id, true, 60 * 60 * 24 * 365 * 1000);
    }
    else
    {
		showhide('block_' + id);
		gid(img).src = 'media/other/open.png';
        delCookie('Block_' + id);
    }
}


function fixAP()
{

    if (gid('_adminBar').style.position == 'absolute')
    {
		gid('_adminBar').style.position = 'fixed';
        setCookie('fixAP', 'ok', 60 * 60 * 24 * 365 * 1000);
		gid('fixAP').innerHTML = 'фиксировать';
    }
    else
    {
		gid('_adminBar').style.position = 'absolute';
        delCookie('fixAP');
		gid('fixAP').innerHTML = 'убрать фиксацию';
    }
}

function delCookie(name)
{
    setCookie(name, getCookie(name), -1);
}

function showhide(id, disable) 
{
	gid(id).style.display = gid(id).style.display == "none" ? "block" : "none";
}

function hide(id)
{
	gid(id).style.display = 'none';
}

function show(id)
{
	gid(id).style.display = 'block';
}

function getConfirm(msg) 
{
	if (confirm(msg))
	{
		return true;
	} 
	else 
	{
		return false;
	}
}


function htmlSpecialChars(str)
{
    str = str.replace('&', '||and||');
    str = str.replace('<', '||men||');
    str = str.replace('>', '||bol||');
	
	return str;
}

function setCheckboxes(the_form, do_check)
{
    var elts      = (typeof(document.forms[the_form].elements['selected_db[]']) != 'undefined')
                  ? document.forms[the_form].elements['selected_db[]']
                  : (typeof(document.forms[the_form].elements['checks[]']) != 'undefined')
          ? document.forms[the_form].elements['checks[]']
          : document.forms[the_form].elements['selected_fld[]'];
    var elts_cnt  = (typeof(elts.length) != 'undefined')
                  ? elts.length
                  : 0;

    if (elts_cnt) 
	{
        for (var i = 0; i < elts_cnt; i++) 
		{
			if(elts[i].checked == true)
			{
				elts[i].checked = false;
			}
			else
			{
				elts[i].checked = true;
			}
        } 
    } else {
		if(elts.checked == true)
		{
			elts.checked = false;
		}
		else
		{
			elts.checked = true;
		}
    }

    return true;
}

function ButtonDisable(form) 
{
	if (document.all || document.getElementById) 
	{
		for (i = 0; i < form.length; i++) 
		{
			var tempobj = form.elements[i]
			if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset") 
			{
				tempobj.disabled = true
			}
		}
	}
}

function ButtonAllow(form) 
{
	if (document.all || document.getElementById) 
	{
		for (i = 0; i < form.length; i++) 
		{
			var tempobj = form.elements[i]
			if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset") 
			{
				tempobj.disabled = false
			}
		}
	}
}

function check (box, max)
{
	var form = box.form;
	var checked = 0;
	if (form)
	{
		for (var i = 0; i < form.elements.length; i++)
		{
			var el = form.elements[i];
			
			if (el.type == 'checkbox' && el.name == 'check[]')
			{
				if (el.checked) checked++;
			}
		}
		if (checked >= max) lock (form, true);
		else lock (form, false);
	}
}

function lock (form, un)
{
    for (var i in form.elements)
    {
        var el = form.elements[i];
        if (el.type == 'checkbox' && el.name == 'check[]')
        {
            if (!el.checked) el.disabled = un;
        }
    }
}

function reloadCaptcha()
{
	gid('captcha').style.background = 'none';
	gid('captcha').innerHTML = "<img src=\"captcha?nocache=" + getrandom() + "\" />";
}

function getrandom() 
{ 

	var min_random = 200; 
	var max_random = 1000; 

	max_random++; 

	var range = max_random - min_random; 
	var n=Math.floor(Math.random()*range) + min_random; 

	return n; 
}

function switchAdminBar()
{

    if (gid('_adminBar').style.display != 'none')
    {
		showhide('_adminBar');
		showhide('_adminBarC');
        setCookie('_adminBarCookie', true, 60 * 60 * 24 * 365 * 1000);
    }
    else
    {
		showhide('_adminBar');
		showhide('_adminBarC');
        delCookie('_adminBarCookie');
    }
}
/*
function genPreview(id, title, shortText, fullText)
{
	var _title = gid(title).value;
	var _short = gid(shortText).value;
	var _full = gid(fullText).value;
	
	if(_title != '' && _short != '')
	{
		gid(id).innerHTML = '<fieldset><legend>Предпросмотр контента</legend> <b>' + _title + '</b><br /><br/>' + parseBB(_short) + (_full != '' ? '<hr />' + parseBB(_full) : '') +'</fieldset>';
	}
	else
	{
		alert('Заполните обязательные поля!');
	}
}

function parseBB(text)
{
	text = text.replace(/\[(left|center|right)\](.*?)\[\/(left|center|right)\]/ig, "<div align=\"$1\">$2</div>");
	text = text.replace(/\[color=(red|blue|black|green|white|yellow|orange|grey)\](.+?)\[\/color\]/ig, "<span style=\"color:$1\">$2</span>");
	text = text.replace(/\[(b|i|u|s)\](.*?)\[\/(b|i|u|s)\]/ig, "<$1>$2</$1>");
	text = text.replace(/\[url=(.+?)\](.+?)\[\/url\]/ig, "<a href=\"$1\">$2</a>");
	text = text.replace(/\[size=([0-9]*)\](.+?)\[\/size\]/ig, "<span style=\"font-size:$1 pt\">$2</span>");
	text = text.replace(/\[hr\]/ig, "<hr />");
	text = text.replace(/\[br\]/ig, "<br />");
	text = text.replace(/\[quote\]/ig, '<div class="quote"><strong>Цитата:</strong><br />');
	text = text.replace(/\[\/quote\]/ig, '</div>');
	return text;
}*/


function changeIcon(path, id)
{
	gid(id).src = path;
}

function in_array(arr, val)
{
   for (key in arr) 
   {
      if (arr[key] == val) 
      {
          return true;
      }
   }
   return false;
}

function resetForm(box)
{
	var form = box.form;
	if (form)
	{
		for (var i in form.elements)
		{
			var el = form.elements[i];
			
			alert(el.type);
		}
	}
}

function genRating(starStyle, starWidth, limitStar, score, votes, blocked, id, module, msg)
{
	var prepCh = (votes == 0) ? 0 : (score/votes)*starWidth;
	var choosen = (prepCh > (starWidth*limitStar)) ? (starWidth*limitStar) : prepCh;
	rat = '<ul class="' + starStyle + '" style="width:' + (starWidth*limitStar) + 'px;">';
	rat += '<li class="choosen" style="width:' + choosen + 'px;" title="' + msg + '">&nbsp;</li>';
	if(blocked == '')
	{
		var num = ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'];
		for(var star=1;star<=limitStar;star++) 
		{
			rat += '<li><a href="javascript:void(0)" onclick="vote(' + id + ', \'' + module + '\', ' + star + ')" class="' + num[star-1] + '-stars">' + star + '</a></li>';
		}
	}
	rat += '</ul>';
	gid('rating'+id).innerHTML = rat;
}

function vote(id, module, num) 
{
	AJAXEngine.async = false;
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('module', module);
	AJAXEngine.setPostVar('num', num);
	AJAXEngine.sendRequest('ajax.php?do=vote', 'rat' + id);
	eval("genRating(" + gid('rat' + id).innerHTML + ");");
}

function do_carma(id, type) 
{
	AJAXEngine.async = false;
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('type', type);
	AJAXEngine.sendRequest('ajax.php?do=carma_vote', 'rating' + id);
}




this.JMY_Ajax = function()
{
    this.replace_id = false;
    this.xmlhttp = false;
    this.postVars = false;
    this.queryType = false;
    this.showedLoadBar = 'loading';
    this.async = true;
    this._response = '';

	this.formatLoader = function()
	{
		var scrollTop = document.documentElement.scrollTop;
		if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) 
			clHeight = document.documentElement.clientHeight;
		else if (document.body && (document.body.clientWidth || document.body.clientHeight)) 
			clHeight = document.body.clientHeight;
		
		topOffset = (scrollTop + (clHeight/3))+'px';
		//document.getElementById(this.showedLoadBar).style.top = topOffset;
	}

	this.showLoader = function(show)
	{
		//if(show == 1)
			//document.getElementById(this.showedLoadBar).style.display = 'block';
	//	else if(show == 0)
			//document.getElementById(this.showedLoadBar).style.display = 'none';
	}
	
    this.run = function ()
    {
        try
        {
            this.xmlhttp = new XMLHttpRequest();
        }
        catch (e)
        {
            try
            {
                this.xmlhttp = new ActiveXObject('Msxml2.XMLHTTP')
            }
            catch (e)
            {
                try
                {
                    this.xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
                }
                catch(e)
                {
                    alert('Хъюстон у нас проблемы...');
                }
            }
        }
		if(this.async == true)
			this.xmlhttp.onreadystatechange = this.onReady;
    }
	
	var _this = this;
    this.onReady = function ()
    {
        if (_this.xmlhttp.readyState == 4)
        {
            if (_this.xmlhttp.status == 200)
            {
				if(this.replace_id != '') document.getElementById(_this.replace_id).innerHTML = _this.xmlhttp.responseText;
				if(this.async == false) _this._response = _this.xmlhttp.responseText;
				if(_this.showedLoadBar != '') _this.showLoader(0);

                this.replace_id = false;
                this.xmlhttp = false;
                this.postVars = false;
				this.showedLoadBar = 'loading';
            }
            else
            {
                if (_this.isAlert)
                    alert(_this.messageConnectError);
            }
        }
    }

    this.setPostVar = function (key, value)
    {

        if (this.postVars == false)
            this.postVars = '';
        else
            this.postVars += '&';

        this.postVars += key + '=' + encodeURIComponent(value);
    }
    
    this.notAsyncReq = function (url)
    {
        if(this.showedLoadBar != '')
        {
            this.formatLoader();
            this.showLoader(1);
        }
        
        var xmlhttp;
        try {
          xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (E) {
                xmlhttp = false;
            }
        }
        
        if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
          xmlhttp = new XMLHttpRequest();
        }
        
        xmlhttp.open('POST', url, false);
        xmlhttp.send(null);
        
        if(xmlhttp.status == 200) {
            if(_this.showedLoadBar != '') _this.showLoader(0);
            return xmlhttp.responseText;
        }
    }

    this.sendRequest = function (url, id)
    {
        this.run();
        this.replace_id = id;
        var noCache = ((-1 < url.indexOf('?')) ? '&' : '?') + 'nocache=' + Math.random();
		
		if(this.showedLoadBar != '')
		{
			this.formatLoader();
			this.showLoader(1);
		}
        this.xmlhttp.open('POST', url + noCache, this.async);
        this.xmlhttp.setRequestHeader('If-Modified-Since', 'Sat, 1 Jan 2000 00:00:00 GMT');
        this.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        if (this.postVars)
            this.xmlhttp.send(this.postVars);
        else
            this.xmlhttp.send(null);
			
		if(this.async == false)
		{
			document.getElementById(this.replace_id).innerHTML = this.xmlhttp.responseText;
			this._response = this.xmlhttp.responseText;
			if(this.showedLoadBar != '') this.showLoader(0);
		}
    }
}

var AJAXEngine = new JMY_Ajax;

function ajaxSimple(uri, id, loader)
{
	AJAXEngine.sendRequest(uri, id);
}

function fastCancel(id, module, type, div)
{
	AJAXEngine.setPostVar('blocked', '');
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('module', module);
	AJAXEngine.setPostVar('type', type);
	AJAXEngine.sendRequest('ajax.php?do=fast_edit', div);
}

function commentCancel(id)
{
	AJAXEngine.setPostVar('blocked', '');
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.sendRequest('ajax.php?do=commentEditAjax', 'comment_'+id);
}

function fileList(id, mod, does, val)
{
	AJAXEngine.setPostVar(does, val);
	AJAXEngine.setPostVar('module', mod);
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.sendRequest('ajax.php?do=fileList', 'filelist');
}


function fast_edit(id, type, module) 
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('module', module);
	AJAXEngine.setPostVar('type', type);
	AJAXEngine.sendRequest('ajax.php?do=fast_edit', type + '-' + id);
}

function fast_post(div) 
{
	AJAXEngine.setPostVar('text', htmlSpecialChars(gid("edit").value));
	AJAXEngine.setPostVar('id', gid("id").value);
	AJAXEngine.setPostVar('module', gid("module").value);
	AJAXEngine.setPostVar('type', gid("type").value);
	AJAXEngine.sendRequest('ajax.php?do=fast_save', div);
}

function initRating(id, module, score, votes)
{

	AJAXEngine.showedLoadBar = '';
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('module', module);
	AJAXEngine.setPostVar('score', score);
	AJAXEngine.setPostVar('votes', votes);
	AJAXEngine.setPostVar('ajaxR', '1');
	AJAXEngine.sendRequest('ajax.php?do=genRating', 'rating_'+id);
}

function dopoll(box, id)
{
	var vote_checks = '';
    var form = gid(box);
	
	if (form)
	{
		for (var i = 0; i < form.elements.length; i++)
		{
			var el = form.elements[i];

			if (el.type == 'checkbox' && el.name == 'check[]')
			{
				if (el.checked) vote_checks = vote_checks + '|' + el.value;
			}
			
			if (el.type == 'radio' && el.name == 'check[]')
			{
				if (el.checked) vote_checks = vote_checks + '|' + el.value;
			}
		}
	}
	
	if(vote_checks)
	{
		AJAXEngine.setPostVar('checks', vote_checks);
		AJAXEngine.setPostVar('pid', id);
		AJAXEngine.sendRequest('ajax.php?do=poll/doVote', id);
	}
	else
	{
		alert('Вы не выбрали ни одного варианта ответа!');
	}
}

function comment_post(id) 
{
	//tinyMCE.triggerSave();
	if(gid('text').value == '') 
	{
		alert('Поле с текстом оставлено пустым!');
		return false;
	}
	
	AJAXEngine.setPostVar('text', htmlSpecialChars(gid("text").value));
	AJAXEngine.setPostVar('id', gid("nid").value);
	AJAXEngine.setPostVar('author', (gid("author") ? gid("author").value : ''));
	AJAXEngine.setPostVar('email', (gid("email") ? gid("email").value : ''));
	AJAXEngine.setPostVar('url', (gid("url") ? gid("url").value : ''));
	AJAXEngine.setPostVar('mod', gid("mod").value);
	AJAXEngine.setPostVar('reply_to', gid("reply_to").value);
	AJAXEngine.setPostVar('securityCode', (gid("securityCode") ? gid("securityCode").value : ''));
	AJAXEngine.setPostVar('subscribe', (gid("subscribe") ? gid("subscribe").value : ''));
	AJAXEngine.setPostVar('commNum', gid('hideCommNum').innerHTML);
	AJAXEngine.setPostVar('g-recaptcha-response', (document.getElementById("g-recaptcha-response") ? document.getElementById("g-recaptcha-response").value : ''));	
	AJAXEngine.sendRequest('ajax.php?do=comment_savea', id);
//	gid('securityCode').value = '';
}

function commentPage(mod, pid, page)
{
	AJAXEngine.setPostVar('mod', encodeURI( mod, true));
	AJAXEngine.setPostVar('pid', pid);
	AJAXEngine.setPostVar('page', page);
	AJAXEngine.setPostVar('commNum', gid('hideCommNum').innerHTML);
	AJAXEngine.sendRequest('ajax.php?do=commentPage', 'commentBox');
}

function commentEditSave(div)
{
	AJAXEngine.setPostVar('text', htmlSpecialChars( gid("edit").value));
	AJAXEngine.setPostVar('id', gid("id").value);
	AJAXEngine.sendRequest('ajax.php?do=commentEditAjaxSave', div);
}

function commentEdit(cid, id)
{
	AJAXEngine.setPostVar('id', cid);
	AJAXEngine.sendRequest('ajax.php?do=commentEditAjax', id);
}

function commentDelete(cid, id, mod, nid, page)
{
	if(getConfirm('Удалить комментарий под №' + cid + '?'))
	{
		AJAXEngine.setPostVar('id', cid);
		AJAXEngine.setPostVar('mod', mod);
		AJAXEngine.setPostVar('nid', nid);
		AJAXEngine.sendRequest('ajax.php?do=commentDeleteAjax', id);
		setTimeout('commentPage(\'' + mod + '\', ' + nid + ', ' + page + ')', 5000);
	}
}

function subscribeComments(does, div)
{
	AJAXEngine.setPostVar('mod', gid("mod").value);
	AJAXEngine.setPostVar('nid', gid("nid").value);
	AJAXEngine.setPostVar('do', does);
	AJAXEngine.sendRequest('ajax.php?do=commentSubscribe', div);
}

function searchList(val, id) 
{
	if(val.length > 3) 
	{
		if(gid(id).style.display=="none")
		{
			showhide(id);
		}
		
		AJAXEngine.showedLoadBar = false;
		AJAXEngine.setPostVar('query', encodeURI(val, true));
		AJAXEngine.sendRequest('ajax.php?do=searchList', id);
	}
}

function check_news(elem, id) 
{
	AJAXEngine.setPostVar('query', encodeURI( gid(elem).value));
	AJAXEngine.sendRequest('ajax.php?do=searchList', id);
}


function addCarma(uid, id)
{
	if(gid('carmaDo').value == '' || gid('carmaText').value == '') 
	{
		alert('Вы не ввели сообщение для пользователя');
		return false;
	}	
	AJAXEngine.setPostVar('uid', uid);
	AJAXEngine.setPostVar('does', gid('carmaDo').value);
	AJAXEngine.setPostVar('text', gid('carmaText').value);
	AJAXEngine.sendRequest('ajax.php?do=addCarma', 'p'+id);
	clearModal_box(id);
}

function adminBlock(action, id, to, from, topos)
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('to', to);
	AJAXEngine.setPostVar('from', from);
	AJAXEngine.setPostVar('topos', topos);
	AJAXEngine.sendRequest('ajax.php?do=' + action, 'blockBox');
}

function adminBlockStatus(id, to)
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('to', to);
	AJAXEngine.sendRequest('ajax.php?do=setBlockStatus', 'blockBox');
}

function userBlockStatus(id, to)
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('to', to);
	AJAXEngine.sendRequest('ajax.php?do=blockStatus', 'blockOk'+id);
	setTimeout('innerEmpt(\'blockOk'+id+'\')', 4000);
}

function adminCommentStatus(id, to)
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('to', to);
	AJAXEngine.sendRequest('ajax.php?do=setCommentStatus', 'statusIcon_'+id);
}


function blockDelete(id)
{
	if(getConfirm('Вы уверены что хотите удалить блок?'))
	{
		AJAXEngine.setPostVar('id', id);
		AJAXEngine.sendRequest('ajax.php?do=deleteBlock', 'blockBox');
	}
}

function userBlockDelete(id)
{
	if(getConfirm('Вы уверены что хотите удалить блок?'))
	{
		AJAXEngine.setPostVar('id', id);
		AJAXEngine.sendRequest('ajax.php?do=userDeleteBlock', 'blockOk'+id);
		setTimeout('innerEmpt(\'blockOk'+id+'\')', 4000);
	}
}

function SaveTitle(mod, id, text)
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.setPostVar('module', mod);
	AJAXEngine.setPostVar('text', text);
	AJAXEngine.sendRequest('ajax.php?do=editTitle', 'editTitle_' + id);
}

function innerEmpt(id)
{
	gid(id).innerHTML = '';
}

function updateCatList(mod, id)
{
	AJAXEngine.setPostVar('mod', mod);
	AJAXEngine.sendRequest('ajax.php?do=getCatByModule', id);
}

function updateReestr(mod, id)
{
	AJAXEngine.setPostVar('org', mod);
	AJAXEngine.sendRequest('ajax.php?do=getReestr', id);
}

function checkLogin(login, id, input)
{
	if(login.length > 1) 
	{
		if(gid(id).style.display=="none")
		{
			showhide(id);
		}
		
		AJAXEngine.showedLoadBar = false;
		AJAXEngine.setPostVar('query', encodeURI( login , true));
		AJAXEngine.setPostVar('input', input);
		AJAXEngine.sendRequest('ajax.php?do=loginList', id);
	}
}

function complitLoad(postId, mod, id)
{
	AJAXEngine.setPostVar('id', postId);
	AJAXEngine.setPostVar('mod', mod);
	AJAXEngine.sendRequest('ajax.php?do=fileList', id);
}

function forumPostEdit(id, divid) 
{
	var addition = '';
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.sendRequest('index.php?url=board/ajax/fastForm', divid);
}

function forumSaveEdit(id2, addition) 
{
	AJAXEngine.setPostVar('text', document.getElementById("edit").value);
	AJAXEngine.setPostVar('id', document.getElementById("id").value);
	if(document.getElementById("files").value == 1) AJAXEngine.setPostVar('files', document.getElementById("files").value);
	AJAXEngine.sendRequest('index.php?url=board/ajax/fastSave'+addition, id2);
}

function forumPostDelete(id, divid)
{
	AJAXEngine.setPostVar('id', id);
	AJAXEngine.sendRequest('index.php?url=board/ajax/delete', divid);
}

function delFriend(uid, div)
{
	AJAXEngine.setPostVar('uid', uid);
	AJAXEngine.sendRequest('ajax.php?do=friendsAjax/delete', div);
}

function addFriend(uid)
{
	AJAXEngine.setPostVar('uid', uid);
	AJAXEngine.sendRequest('ajax.php?do=friendsAjax/add', 'friendDo');
}

function acceptFriend(uid, div)
{
	AJAXEngine.setPostVar('uid', uid);
	AJAXEngine.sendRequest('ajax.php?do=friendsAjax/accept', div);
}

function blogRating(id, div)
{
	AJAXEngine.setPostVar('pid', id);
	AJAXEngine.sendRequest('index.php?url=blog/ajaxRating', div);
//async	
}