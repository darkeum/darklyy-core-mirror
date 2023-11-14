class PTOPrice extends Model{
    url(){
        const pto_id = $("#pto_id").val();
        return `/administration/module/pto/price/${pto_id}`;
    }
}