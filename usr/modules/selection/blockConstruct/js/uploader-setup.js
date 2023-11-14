var maxFileSize = 104857600;
var fileSize = 0;

function adq(e){
    
    
    $('.box-modal .shag-content, .box-modal .modal-form-bot').css('display','none');
    $('.box-modal .submiltLoader').css('display','block');

    
    $('.box-modal .userfile').each(function(){
        
        var file = $(this)[0].files[0];
        if(file){
            //alert(file.size);
            fileSize += file.size;
        }
        
    });
    
    
    if(fileSize <= 0){
        
        $('.box-modal .raschetStoimostyLoading progress').css('display','none');
        
    }
    
    
    $('.box-modal .userfile').each(function(ind){
        
        
        var file = $(this)[0].files[0];
        
        // проверка на размер файла
        if (file.size >= maxFileSize) {
            $('.box-modal .result').html('Слишком большой размер файла');
            return false;
        }
        
        if (file) {
            
            upload(file);
        
        }
    
        
    });
     
    
}

var ttl;
var thLoad = 0;
var findex = 0,
filesId = '';


function upload(file) {
    var ajax = new XMLHttpRequest();
    ajax.upload.onprogress = function(event) {
        $('.box-modal .uploaded').html(((thLoad + event.loaded) / 1024 / 1024).toFixed(2) + ' Мб <b>/</b> ' + (fileSize/1024/1024).toFixed(2) + ' Мб');
        ttl = event.total;
        
        $('.box-modal .progress').attr('max', fileSize);
        $('.box-modal .progress').val(thLoad + event.loaded);
        
        
        if(event.total <= event.loaded){
        
            thLoad += event.total;
        
        }
        
        
    }

    ajax.onload = ajax.onerror = function() {


        if (this.status == 200) {
        
    		if(findex != 0) {filesId += '|';}	
    		
            filesId += this.responseText;   
               
            //Файл загружен
            if(thLoad + event.loaded > fileSize){
                
                sbSave();
                
            }
            
            findex++;
       
            
        } else {
      
            $('.box-modal .result').html('<span style="color: red">Не удалось загрузить файл</span>');
        }
    }

    var formData = new FormData();
    formData.append("userfile", file);
    //formData.append("modul", $('#modulName').val());
    
    ajax.open("POST", "/selection/ajax", true);
    ajax.send(formData);//async: false
    
}


function alertObj(obj) { 
    var str = ""; 
    for(k in obj) { 
        str += k+": "+ obj[k]+"\r\n"; 
    } 
    alert(str); 
} 


var indFile = 2;

function botLoadFileName(){
    
        $('.box-modal .fileLoadBlockDiv:last').after('<div class="fileLoadBlockDiv">'+indFile+'. <input type="file" name="userfile" class="userfile"></div>');
        
        indFile++;
    
}
