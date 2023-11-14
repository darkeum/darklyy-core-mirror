$(function () {
  const select2 = ["#category", "#section", "#unit", "#man", "#types", ".select_attribute"];
  select2.forEach((value) => {
    initSelect(value);
  });

  $("#category").on("change", function () {
    loadAttribute();
  });

  function initSelect(value){
    $(value).select2({ theme: "bootstrap-5" });
  }

  function getFormData(){
      const form = new FormData(document.getElementById("tab-main-form"));
      form.delete("_method");
      return form;
  }

  async function loadAttribute(){
    const attribute = $("#attribute");
    attribute.html(`<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>`);
    attribute.html(await (await fetch("/administration/module/catalog/get_attributes", {
        method: "POST",
        body: getFormData(),
    })).text());
    initSelect(".select_attribute");
  }

  loadAttribute();

  $("#skuGenerate").on("click", async function(){
      const class_name = "fa-spin";
      const children = $(this).children();
      children.addClass(class_name);
      $(this).prop("disabled", true);
      const data = await ( await fetch("/administration/module/catalog/sku_generate", {
          method: "POST",
          body: getFormData(),
      }) ).json();
    $("#sku").val(data.value);
    children.removeClass(class_name);
    $(this).prop("disabled", false);
  });
});
