function string_to_number(value){
    if(typeof value === "number"){
        return value;
    }
    const new_value = Number(value.replace(/ /g, "").replace(/,/g, ".").replace(/	/g, "").replace("руб.", "").replace("%", "").replace(/\s+/g, ""));
    return new_value;
}

function priceSet(value){
    const new_value = String(value).replace(/,/g, '.');
	return Number.prototype.toFixed.call(parseFloat(new_value) || 0, 2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ') + ' руб.';
}

//get_element_value_by_id
function gevbi(id){
    return $(`#${id}`).val();
}

//get_this_value
function gtv(This){
    return $(This).val();
}

//get_this_value_text
function gtvt(This){
    return $(This).find('option:selected').text();
}

//get_this_text
function gtt(This){
    return $(This).text();
}

//get_this_data
function gtd(This, data){
    return $(This).data(data);
} 

function parse_date_rus(d){
    const new_date = ("0" + d.getDate()).slice(-2) + "." + ("0"+(d.getMonth()+1)).slice(-2) + "." + d.getFullYear();
    return new_date;
}

function parse_date_en(d){
    const new_date = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
    return new_date; 
}