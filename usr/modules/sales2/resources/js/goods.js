function create_items_sales(items){
  items.forEach((item) => {
    vm.init_mark(item);
    item.create_ = true;
    vm.sales_detail.push(item);
  });
  vm.re_price();
  vm.re_purchase();
  vm.init_binding_list();
  vm.sort_items();
}
async function append_goods(form) {
  const items = await (
    await fetch(router.victoria_append_prepare, {
      method: "POST",
      body: form,
    })
  ).json();
  create_items_sales(items);
}

async function replace_good(index, cid) {
  const lw = new LW();
  lw.on();
  const item = vm.sales_detail[index];
  const form = new FormData();
  form.append("sales_id", vm.sales.id);
  form.append("cid", cid);
  const prepare_item = await (
    await fetch(router.victoria_replace_prepare, {
      method: "POST",
      body: form,
    })
  ).json();
  for (key in prepare_item) {
    switch (key) {
      default:
        item[key] = prepare_item[key];
        break;
      case "position":
      case "count":
        break;
    }
  }
  item.replace_ = true;
  vm.re_price_data_item(item);
  vm.re_price();
  vm.re_purchase();
  vm.init_binding_list();
  lw.off();
}

async function clipboard_sales(data, options){
  try {
    const form = new FormData;
    form.append("data", data);
    const new_data = await ( await fetch(options.link, {
      method: "POST",
      body: form,
    }) ).json();
    const form2 = new FormData;
    form2.append("sales_id", vm.sales.id);
    form2.append("sales_detail", JSON.stringify(new_data));
    const prepare_items = await ( await fetch(router.ajax.clipboard_prepare, {
      method: "POST",
      body: form2,
    }) ).json();
    create_items_sales(prepare_items);
  } catch (error) {
    Swal.fire(error.message)
  }
}