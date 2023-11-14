$(function () {
  const option = {
    theme: "bootstrap-5",
  };

  const categoryAttributeCreate = $("#categoryAttributeCreate");
  const categoryAttributeUpdate = $("#categoryAttributeUpdate");
  const category = $("#category");
  const attribute = $("#attribute");
  const attribute_all = $("#attribute_all");
  const editAttributeCategory = $("#editAttributeCategory");

  category.select2(option);
  attribute.select2(option);
  categoryAttributeCreate.select2(option);
  categoryAttributeUpdate.select2(option);
  attribute_all.select2(option);

  const attributeCreateModal = new bootstrap.Modal(document.getElementById("attributeCreateModal"));
  const attributeUpdateModal = new bootstrap.Modal(document.getElementById("attributeUpdateModal"));

  const attributeValueCreateModal = new bootstrap.Modal(document.getElementById("attributeValueCreateModal"));
  const attributeValueUpdateModal = new bootstrap.Modal(document.getElementById("attributeValueUpdateModal"));

  //Добавление
  $("#attributeFormCreateModal").on("submit", async function (e) {
    e.preventDefault();
    const url = $(this).attr("action");
    const data = new FormData(this);
    const lw = new LW();
    lw.on();
    try {
      await attributeSend(url, data);
      await Promise.all([load_attribute(), load_all_attribute()]);
    } catch (error) {
      alert(error.message);
    }
    attributeCreateModal.hide();
    lw.off();
  });
  $("#attributeFormUpdateModal").on("submit", async function (e) {
    e.preventDefault();
    const id = $(this).data("id");
    const url = $(this).attr("action") + id;
    const data = new FormData(this);
    data.append("_method", "PUT");
    const lw = new LW();
    lw.on();
    try {
      await attributeSend(url, data);
      vm.attribute_data = await load_attribute_data(id);
      await Promise.all([load_attribute(), load_all_attribute()]);
    } catch (error) {
      alert(error.message);
    }
    attributeUpdateModal.hide();
    lw.off();
  });

  async function attributeSend(url, data){
    return await (
      await fetch(url, {
        method: "POST",
        body: data,
      })
    ).json();
  }

  category.on("change", async function () {
    vm.category = $(this).val();
    vm.attribute_show = false;
    await load_attribute();
  });

  async function load_attribute() {
    const category_id = category.val();
    if (category_id != null) {
      const attribute_list = await (
        await fetch(
          `/administration/module/catalog/attribute/load/${category_id}`
        )
      ).json();
      vm.attribute_list = attribute_list;
      editAttributeCategory.prop("disabled", false);
      attribute.empty();
      attribute.append(
        `<option value="" selected disabled>Выберите атрибут</option>`
      );
      attribute_list.forEach((item) => {
        attribute.append(
          `<option value="${item.id}">${item.name} (${item.id})</option>`
        );
      });
      attribute.val(vm.attribute).trigger("change");
      sort_init(
        document.getElementById("attributeCategoryItemsModal"),
        ".attributeCategoryItemMove",
        async (evt) => {
          vm.attribute_list.splice(
            evt.newIndex,
            0,
            vm.attribute_list.splice(evt.oldIndex, 1)[0]
          );
          const new_items = [];
          vm.attribute_list.forEach((item, index) => {
            item.attribute_category_one.position = index;
            new_items.push(item.attribute_category_one);
          });
          await catalog_attribute_category_update(new_items);
        }
      );
    } else {
      editAttributeCategory.prop("disabled", true);
    }
  }

  async function catalog_attribute_category_update(items){
    return await (
      await fetch(
        `/administration/module/catalog/attribute/category/mass_update`,
        {
          method: "POST",
          body: JSON.stringify({
            items,
          }),
          headers: {
            "Content-Type": "application/json",
          },
        }
      )
    ).json();
  }

  function sort_init(container, handle, onEnd) {
    return new Sortable(container, {
      handle,
      direction: "vertical",
      forceAutoscrollFallback: true, // force autoscroll plugin to enable even when native browser autoscroll is available
      scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
      scrollSpeed: 10, // px, speed of the scrolling
      bubbleScroll: true, // apply autoscroll to all parent elements, allowing for easier movement
      onEnd,
    });
  }

  async function load_all_attribute(attribute_list = null) {
    if (attribute_list == null) {
      attribute_list = await (
        await fetch(`/administration/module/catalog/attribute/load_all`)
      ).json();
    }

    attribute_all.empty();
    attribute_all.append(
      `<option value="" selected disabled>Выберите атрибут</option>`
    );
    attribute_list.forEach((item) => {
      attribute_all.append(
        `<option value="${item.id}">${item.name} (${item.id})</option>`
      );
    });
    attribute_all.val(vm.attribute).trigger("change");
  }

  async function load_attribute_data(attribute_id) {
    return await (
      await fetch(
        `/administration/module/catalog/attribute/get/${attribute_id}`
      )
    ).json();
  }

  $("#addAttributeModal").on("click", function () {
    $("#nameAttributeCreate").val("");
    categoryAttributeCreate.val(null).trigger("change");
    attributeCreateModal.show();
  });

  attribute.on("change", async function () {
    await getAttributeInfo($(this).val());
  });

  attribute_all.on("change", async function () {
    await getAttributeInfo($(this).val());
  });

  async function getAttributeInfo(attribute_id) {
    vm.attribute = attribute_id;
    if (vm.attribute != null) {
      vm.attribute_data = await load_attribute_data(vm.attribute);
      vm.attribute_show = true;
      await vm.$nextTick();
      sort_init(
        document.getElementById("attribute_items"),
        ".attribute_item_value-move",
        async (evt) => {
          vm.attribute_data.data.attribute_value.splice(
            evt.newIndex,
            0,
            vm.attribute_data.data.attribute_value.splice(evt.oldIndex, 1)[0]
          );
          vm.attribute_data.data.attribute_value.forEach((item, index) => {
            item.position = index;
          });
          await (
            await fetch(
              `/administration/module/catalog/attribute/value/mass_update`,
              {
                method: "POST",
                body: JSON.stringify({
                  items: vm.attribute_data.data.attribute_value,
                }),
                headers: {
                  "Content-Type": "application/json",
                },
              }
            )
          ).json();
        }
      );
    }
  }

  function addInputAttributeId(attribute_id, type) {
    $(`#attributeValueInputs${type}Modal`).html("");
    $(`#attributeValueInputs${type}Modal`).append(
      `<input type="hidden" name="attribute_id" value="${attribute_id}">`
    );
  }

  $("#attributeValueFormCreateModal").on("submit", async function(e){
    e.preventDefault();
    const url = $(this).attr("action");
    const body_data = new FormData(this);
    const lw = new LW();
    lw.on();
    try {
      const data = await attributeValueSend(url, body_data);
      attributeValueCreateModal.hide();
      vm.attribute_data = await load_attribute_data(data.attribute_id);
    } catch (error) {
      alert(error.message);
    } finally{
      lw.off();
    }
  });
  $("#attributeValueFormUpdateModal").on("submit", async function(e){
    e.preventDefault();
    const id = $(this).data("id");
    const url = $(this).attr("action") + id;
    const body_data = new FormData(this);
    body_data.append("_method", "PUT");
    const lw = new LW();
    lw.on();
    try {
      const data = await attributeValueSend(url, body_data);
      attributeValueUpdateModal.hide();
      vm.attribute_data = await load_attribute_data(data.attribute_id);
    } catch (error) {
      alert(error.message);
    } finally{
      lw.off();
    }
  });

  async function attributeValueSend(url, body_data){
    return await (
      await fetch(
        url,
        {
          method: "POST",
          body: body_data,
        }
      )
    ).json();
  }

  function isAdmin(){
    return adminUsers.includes(uid);
  }

  const app = Vue.createApp({
    compilerOptions: {
      delimiters: ["${", "}"],
      comments: true,
    },
    data() {
      return {
        category: null,
        attribute: null,
        attribute_show: false,
        isAdmin: isAdmin(),
        attribute_data: {},
        attribute_list: [],
        isAttributeEdit: false,
      };
    },
    methods: {
      editAttributeModal() {
        const data = vm.attribute_data.data;
        $("#nameAttributeUpdate").val(data.name);
        const cat = [];
        data.attribute_category.forEach((item) => {
          cat.push(item.category_id);
        });
        categoryAttributeUpdate.val(cat).trigger("change");
        $(`#type_update_${data.type}`).prop("checked", true);
        $("#attributeFormUpdateModal").data("id", data.id);
        attributeUpdateModal.show();
      },
      deleteAttributeModal() {
        Swal.fire({
          title: "Вы уверены что хотите удалить?",
          showDenyButton: true,
          confirmButtonText: "Удалить",
          denyButtonText: `Отмена`,
        }).then(async (result) => {
          if (result.isConfirmed) {
            const lw = new LW();
            lw.on();
            try {
              await (
                await fetch(
                  `/administration/module/catalog/attribute/${this.attribute}`,
                  {
                    method: "DELETE",
                  }
                )
              ).json();
              this.attribute = null;
              this.attribute_show = false;
              await Promise.all([load_attribute(), load_all_attribute()]);
            } catch (error) {
              alert(error.message);
            } finally {
              lw.off();
            }
          }
        });
      },
      addAttributeValueModal() {
        $("#attributeValueInputCreateModal").val("");
        addInputAttributeId(this.attribute, "Create");
        attributeValueCreateModal.show();
      },
      editAttributeValueModal(item) {
        $("#attributeValueInputUpdateModal").val(item.value);
        addInputAttributeId(item.attribute_id, "Update");
        $("#attributeValueFormUpdateModal").data("id", item.id);
        attributeValueUpdateModal.show();
      },
      deleteAttributeValueModal(item) {
        Swal.fire({
          title: "Вы уверены что хотите удалить?",
          showDenyButton: true,
          confirmButtonText: "Удалить",
          denyButtonText: `Отмена`,
        }).then(async (result) => {
          if (result.isConfirmed) {
            const lw = new LW();
            lw.on();
            try {
              await (
                await fetch(
                  `/administration/module/catalog/attribute/value/${item.id}`,
                  {
                    method: "DELETE",
                  }
                )
              ).json();
              this.attribute_data = await load_attribute_data(
                item.attribute_id
              );
            } catch (error) {
              alert(error.message);
            } finally {
              lw.off();
            }
          }
        });
      },
      async catalog_attribute_category_update(catalog_attribute_category){
        await catalog_attribute_category_update([catalog_attribute_category]);
      }
    },
  });

  const vm = app.mount("#attribute_data");

  load_all_attribute(attributes);
});
