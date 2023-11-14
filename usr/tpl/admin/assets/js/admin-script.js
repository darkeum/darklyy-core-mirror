function createCheckbox(id, request = {}){
    const div = document.createElement("div");
    div.classList.add("checkbox-custom", "mb15");
    const name_id = `checkbox${id}`;
    const input = document.createElement("input");
    input.id = name_id;
    input.type = "checkbox";
    input.value = id;
    input.name = "checks[]";
    input.classList.add("checks");
    const main = request?.main ?? false;
    if(main){
        input.addEventListener('change', function(){
            document.querySelectorAll("input.checks").forEach(item => {
                if(this != item){
                    item.checked = this.checked;
                }
            })
        })
    } 
    const label = document.createElement("label");
    label.setAttribute("for", name_id);
    div.append(input, label);
    return div;
}