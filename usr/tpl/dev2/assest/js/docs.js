var file_types=Array({'c':'word','ext':Array('doc','docx','rtf')},{'c':'pdf','ext':Array('pdf')},{'c':'excel','ext':Array('xls','xlsx')},{'c':'picture','ext':Array('png','gif','jpg','jpeg','bmp')},{'c':'rar','ext':Array('rar')},{'c':'zip','ext':Array('zip','gzip','gz','tar')},{'c':'psd','ext':Array('psd')},{'c':'notepad','ext':Array('txt')},{'c':'ppt','ext':Array('ppt','pptx','pps','ppsx')},{'c':'avi','ext':Array('avi','mpeg','3gp','flv','swf')}
        );$(document).ready(function(){
            /*var arr1 = [];
            var arr2 = [];
            var cnt=0;
            $('p.download').each(function(){
                var href=$(this).find('a').attr('href');
                var ext=href.substr(href.lastIndexOf('.')+1);
                var c='warning';
                for(var i=0;i<file_types.length;i++)
                    if($.inArray(ext,file_types[i].ext)>=0)
                        c=file_types[i].c;
                $(this).addClass(c).addClass('files1'+cnt);
                arr1[cnt]=href;
                arr2[cnt]='.files1'+cnt;
                cnt++;
            });*/
            /*for(var i1 in arr1)
            $.get(wwwPath+'ajax.php?file='+arr1[i1],function(t1){
                if(t1)
                $(arr2[i1]).append('<span class="size">('+t1+')</span>');
            });*/
            var arr11 = [];
            var arr21 = [];
            var cnt1=0;
            $('a.download').each(function(){
                var href=$(this).attr('id');
                var ext=attr('id');
                var c='warning';
                for(var i=0;i<file_types.length;i++)
                    if($.inArray(ext,file_types[i].ext)>=0)
                        c=file_types[i].c;
                $(this).addClass(c).addClass('files2'+cnt1);
                arr11[cnt1]=href;
                arr21[cnt1]='.files2'+cnt1;
                cnt1++;
            });
            for(var i2 in arr11){
            usleep(1);
                        }
        });