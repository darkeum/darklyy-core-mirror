class PTOView extends Model{
    url(){
        const pto_id = $("#pto_id").val();
        return `/administration/module/pto/view/${pto_id}`;
    }
}