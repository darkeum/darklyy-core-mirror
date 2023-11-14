$(function(){
    $extendedSort.sort_update = () => {};
    $extendedSort.sort_update_items = async request => {
        const new_order = await ( await fetch(router.ajax.extended_sort, {
            method: "POST",
            body: request
        }) ).json();
        vm.sort_items(new_order);
        vm.sales_detail = vm.sales_detail.sort((a,b)=>(a.position - b.position))
        vm.init_binding_list();
    };
})