<div class="profile-head mb-3 row">
	<div class="col-6 col-md-12 ">
		<h1>Мои спецификации</h1>
	</div> 
	<div class="col-md-6 d-none d-sm-block">		
		<ul id="breadcrumbs-two" class="breadcrumb breadcrumb-profile">
			<li><a href="/">Главная</a><span class="go-page"></span></li>
			<li><a href="/profile">Профиль</a><span class="go-page"></span></li>
			<li class="active"><span>Мои спецификации</span></li>		
		</ul>
	</div>
	<div class="col-6 col-md-6  text-right">
		<div class="btn-group" role="group" id="addButtonOrg">	
			<span data-toggle="tooltip" data-placement="top" title="Добавить организацию">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#orgAdd">Добавить организацию</button>	
			</span>
		</div>
		<div class="btn-group " role="group" id="addButtonOrder" style="display: none;">	
			<span data-toggle="tooltip" data-placement="top" title="Добавить заявку">
				<button type="button" class="btn btn-secondary text-uppercase" data-toggle="modal" data-target="#orderAdd">Добавить заявку</button>	
			</span>
		</div>
	</div>   
</div>


<div id="about" class="main-container">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
               
                <div id="result" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>
    <script>
    $(function(){
    //загрузка основного списка
        $.ajax({
            url: "profile/specification_list",
            type: "POST",
            success:function(data){
                $("#result").html(data);
                setTimeout(function(){
                    $("#result").fadeIn();
                }, 300);
            }
        });
        //загрузка detail
        $(document).on("click", ".spec", function(){
            //console.log($(this).attr("id"));
            let mas = $(this).attr("id").split("_");
            let iid = mas[1];
            let id = mas[2];
            //console.log(iid);
            //console.log(id);
            if($("#sr_"+iid).css("display") == "block"){
                $("#sr_"+iid).slideToggle();
            } else {
                $.ajax({
                url: "profile/specification_list_detail",
                type: "POST",
                data: {id: id},
                success:function(data){
                //console.log(data);
                
                let myData = JSON.parse(data);
                let str = '<ol>';
                for(key in myData){
                    str += "<li>"+myData[key]["name"]+"</li>";
                }
                str += "</ol>";
                $("#sr_"+iid).html(str);
                $("#sr_"+iid).slideToggle();
                }
            });
            }
         
        });
        //восстановление спецификации
        $(document).on("click", ".restoreSpec", function(){
            localStorage.removeItem("basketwidjet");
            localStorage.removeItem("basketwidjet_ids");
            let id = $(this).attr("id").slice(12);
            //console.log(id);
            $.ajax({
                url: "profile/specification_list_detail",
                type: "POST",
                data: {id: id},
                success:function(data){
                    localStorage.setItem("basketwidjet", data);
                    let mas = [];
                    let i = 0;
                    let myData = JSON.parse(data);
                    for(key in myData){
                        mas[i] = key;
                        i++;
                    }
                    localStorage.setItem("basketwidjet_ids", JSON.stringify(mas));
                }
            });
        });
        
        //удаление спецификации
        $(document).on("click", ".removeSpec", function(){
            let id = $(this).attr("id").slice(11);
            console.log(id);
            $.ajax({
                url: "profile/removeSpec",
                type: "POST",
                data: {id: id},
                success:function(data){
                    $("#main_"+id).hide();
                }
            });
        });

        $(document).on("click", ".editSpec", function(){
            let id = $(this).data("id");
            swal("Наименование спецификации:", {
                content: "input",
                attributes: {
                    placeholder: "Введите наименование спецификации",
                }
            }).then((value) => {
                    if(value){
                        swal({
                        title: "Успешно!",
                        icon: "success",
                    });
                    $.post("profile/update_name_spec", {id:id, name:value}, function(){
                        $.post("profile/specification_list", function(data){
                            $("#result").html(data);
                        })
                    });
                }
            });
            $(".swal-content__input").val($(this).data("name"));
        });

        $(document).on("click", ".getSpec", function(){
            let id = $(this).data("id");
            $.post("profile/getJsonSpec", {id:id}, function(data){
                Cookies.set('cart_products', data);
                document.location.href = "/catalog_v2/specification";
                setTimeout(function(){
                    Cookies.remove('cart_products');
                }, 2000)
            });
        });

    });
    </script>