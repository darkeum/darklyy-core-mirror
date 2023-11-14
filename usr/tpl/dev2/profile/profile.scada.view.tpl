<script>
	function getScadaAll(){
		$.post("ajax.php?do=getScadaAll", {id:{%ID%}}, function(data){
			$("#result").html(data);
		});	
	}
	getScadaAll();
	let intervalScada = setInterval(() => {getScadaAll();}, 1000);
	function setScada(){
	let tag = $('#tagId').val();
	let value = $('#val').val();
	
	$.post("ajax.php?do=setCoilByTag", {tag, value}, function(data){
			getScadaAll();
	});	
	}
</script>

<div id="about" class="content col-md-12 fadeInUp">
	<div class="row">
		<div class="col-md-6">
		<div id="result" ></div>
		</div>
		<div class="col-md-6">
		
		<div class="input-group mb-3">
  
  <select class="custom-select" id="tagId" name = "tagId">
   {%WRITE_TAG%}
  </select>
   <select class="custom-select" id="val" name = "val">
<option value="true">True</option>
<option value="false">False</option>
  </select>
   <div class="input-group-prepend">
    <button class="btn btn-outline-secondary" type="button" onclick="setScada();">Отправить</button>
  </div>
</div>
		
		
		
		
		
		
		
		</div>
	</div>
</div>

