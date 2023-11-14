$(document).ready(function(){
    
    if(reghimTp == 1){
        
        $('.blockLeftCont[data-type="block_podpitky"]').css('display','none');
        $('.calculateCost').attr('onclick','calculateOneCost()');
    
    }
    
});

function vetka2(){
    
    
    if(reghimTp == 1){
        
        $('.blockLeftCont').css('display','block');
        $('.blockLeftCont[data-type="block_podpitky"]').css('display','none');
        
        
        $('#blockOneRightWrap .blockActiveHover').removeClass('blockActiveHover');
        
        $('.mapOneBlockDrop .fa-trash').attr('onclick','deleteBlockOne(this)');
        
        
        $('.mapOneBlockDrop img[data-type="block_vvoda"]').parents('.dropBlock').attr("onclock","modalGenerator('block_vvoda','block_vvoda','',this)");
     
        
    }
    
    
}

function deleteBlockOne(aThis){
    
    statusDelete = false;

    var oneHoverBlock = '<div class="divBotPosition"><i class="fa fa-pencil"></i> <i class="fa fa-trash" onclick="deleteBlockOne(this)"></i></div><div class="insertBlockNew insertBlockNull" onclick="menuType()"><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_vvoda\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\')); ">Вставить блок ввода</span></div><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_gvs\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\'))">Вставить блок ГВС</span></div><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_otopleniya\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\'))">Вставить блок отопления</span></div><div class="clickDpMenu insertBlockNewMax"><span onclick="modalGenerator(\'block_podpitky\',\'\',\'\',$(this).parents(\'.mapOneBlockDrop\'))">Вставить блок подпитки</span></div><div class="clickDp"><div class="iconHover"></div><span>Вставить блок</span></div></div>';
    $('.mapOneBlockDrop').addClass('blockActiveHover').html(oneHoverBlock);

    //conclusionLineStart();
    
}

function calculateOneCost(){


    if($('.mapOneBlockDrop .spanHidden').text() != ''){
        
        calculateCostZp(2);
        
    }
    
    
}