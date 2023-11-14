function init_head() {
  const url = router.ajax.fast_change_sales;
  const emptytext = "Не указано";

  const stock_view = document.getElementById("stock_view");
  const sales_type = document.getElementById("sales_type");
  vm.editable = {
    title: new DarkEditable(document.getElementById("title"), {
      type: "text",
      title: "Введите заголовок",
      emptytext: "Отсутствует",
      url,
    }),
    date: new DarkEditable(document.getElementById("date"), {
      type: "date",
      title: "Выберите дату",
      viewformat: "DD.MM.YYYY",
      emptytext,
      url,
    }),
    sales_type: new DarkEditable(sales_type, {
      type: "select",
      emptytext,
      url,
    }),
    org: new DarkEditable(document.getElementById("org"), {
      type: "select",
      emptytext,
      url,
    }),
    stock_id: new DarkEditable(document.getElementById("stock_id"), {
      type: "select",
      emptytext,
      url,
    }),
    stock_view: new DarkEditable(stock_view, {
      type: "select",
      emptytext,
    }),
    source_appeal: new DarkEditable(document.getElementById("source_appeal"), {
      type: "select",
      emptytext,
      url,
    }),
    market_segment: new DarkEditable(
      document.getElementById("market_segment"),
      {
        type: "select",
        emptytext,
        url,
      }
    ),
    by_field_activity: new DarkEditable(
      document.getElementById("by_field_activity"),
      {
        type: "select",
        emptytext,
        url,
      }
    ),
    sales_cat: new DarkEditable(document.getElementById("sales_cat"), {
      type: "select",
      emptytext,
      url,
    }),
  };

  stock_view.addEventListener('save', async function(){
    Cookies.set(stock_id_view_name, vm.editable.stock_view.value);
		await vm.refresh();
  })
  sales_type.addEventListener('save', async function(){
    vm.sales.sales_type = vm.editable.sales_type.getValue();
  })
}

init_head();