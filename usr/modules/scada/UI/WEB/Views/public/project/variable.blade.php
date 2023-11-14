{% extends "@scada/public/project/holm/layout.html.twig" %}

{% block main %}
<script>
    function getScadaAll(){
        $.get("{{router('scada.ajax')}}", function(data){
            try {
                let json = JSON.parse(data);
                json.forEach(function(i) {
                    //console.log(i.value);
                    let value = i.last_value;
                    if(i.last_value === false) {
                        value = 'false';
                    } else {
                    }
                    $("#tag-"+i.id).html(value);
                });
            } catch (e) {
                return true;
            }
        });	
    }
    getScadaAll();
    let intervalScada = setInterval(() => {getScadaAll();}, 5000);
   
</script>
<div id="result"></div>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<h2>Переменные</h2>
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Название</th>
            <th scope="col">Адрес</th>
            <th scope="col">Тип</th>
            <th scope="col">Значение</th>
          </tr>
        </thead>
        <tbody>
            {% for tag in tags %} 
            <tr>
                <td>{{ tag.id }}</td>
                <td>{{ tag.title }}</td>
                <td>{{ tag.address }}</td>
                <td>{{ tag.type }}</td>
                <td><div id="tag-{{ tag.id }}">NULL</div></td>
            </tr>
            {% endfor %}           
        </tbody>
      </table>
    </div>
</main>
{% endblock main %}