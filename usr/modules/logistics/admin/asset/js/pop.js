$("form").submit(function () {
  let postForm = { 
   'price': $('#price').val(), 
   'id': $('#id').val(),
  };
  let modal="#syncModal-" + postForm['id'];
  $(modal).modal('hide');
  $.ajax({
   type: 'POST',
   url: '/administration/module/logistics/delivery_archive/fast-price-update', 
   data: postForm,
   dataType: 'json'
  });
    reload_grid();
   return false;
  });


function reload_grid(){
    $("#jqLogisticsDeliveryArchive").trigger("reloadGrid");
}