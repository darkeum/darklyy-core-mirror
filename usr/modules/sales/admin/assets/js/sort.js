$(function(){
    $("#tov_list").sortable({
      update: function( event, ui ) {
        const position = $(".position");
        const json = {};
        position.each(function(index) {
          const i = index + 1;
		      $(this).html(i);
          json[ $(this).data("mainid") ] = i;
        });
        const res = JSON.stringify(json);
        $.post("ajax.php?do=setPosition", {res});
      },
      axis: "y",
      handle: ".position"
    });
});