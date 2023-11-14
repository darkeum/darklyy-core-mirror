$logisticsDelivery = {
    modal: $("#deliveryModal"),
    modal_form: $("#deliveryModalForm"),
    after_send: async () => {},
    open: async function() {
        if(await this.is_create()){
            swal("Внимание!", "Документ уже был отправлен на доставку", "warning");
        } else {
            this._open();
        }
    },
    _open: async function(){
        this.modal.modal("show");
    },
    is_create: async function() {
        const data = this.modal_form.serialize();
        return await $.post("/administration/module/logistics/delivery/is_create", data);
    },
    after_cancel_delivery: async () => {},
    cancel_delivery: async function(){
        swal({
          title: "Вы уверены?",
          text: "Отменить доставку",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then(async (willCancel) => {
          if (willCancel) {
            const lw = new LW;
            lw.on();
            const data = this.modal_form.serialize();
            await $.post("/administration/module/logistics/delivery/cancel", data);
            this.after_cancel_delivery();
            lw.off();
            swal("Успешно!", "Доставка отменена", "success");
          }
        });
    },
}
$(function(){
    $logisticsDelivery.modal_form.on("submit", async function(e){
        e.preventDefault();
        $logisticsDelivery.modal.modal("hide");
        const request = $(this).serialize();
        const lw = new LW;
        lw.on();
        await delivery_add(request).then(data => {
            if(data.count > 0){
                swal("Успешно!", "Заявка успешно отправлена", "success");
            } else {
                if(data.error_message){
                    swal("Внимание!", data.error_message, "warning");
                } else {
                    swal("Внимание!", "Позиции не были отправлены", "warning");
                }
            }
        }, error => {
            swal("Ошибка!", error, "error");
        });
        await $logisticsDelivery.after_send();
        lw.off();
    });
    $("#delivery_type").on("change", function(){
        const type = +$(this).val();
        const delivery_price_div = $("#delivery_price_div");
        const delivery_price = $("#delivery_price");
        switch(type){
            default:
                delivery_price_div.show();
                delivery_price.val("");
            break;
            case 3:
                delivery_price_div.hide();
                delivery_price.val(0);
            break;
        }
    });
    
    async function delivery_add(request){
        const json = await $.post("/administration/module/logistics/delivery/add", request);
        try{
            const data = JSON.parse(json);
            return Promise.resolve(data);
        }catch(e){
            return Promise.reject(`\${e} (\${json})`);
        }
    }
});

//Map

const ym = {
    init(){
        this.mapContainer = $('#map');
        this.dma = "deliveryModalAddress";
        this.placemark = false;
        this.suggestView = new ymaps.SuggestView(this.dma);
        this.map = new ymaps.Map('map', {
            center: [56.852676, 53.206891],
            zoom: 10
        }, {
            searchControlProvider: 'yandex#search'
        });
        this.dma_change();
        this.map_click();
    },
    dma_change(){
        const This = this;
        $(`#${this.dma}`).on("change", async function(){
            const value = $(this).val();
            if(value){
                await This.geocode(value);
                await This.createMap();
            }
        });
    },
    map_click(){
        const This = this;
        this.map.events.add('click', async function (e) {
            const coords = e.get('coords');
            await This.geocode(coords);
            This.coords = coords;
            await This.createMapClick();
            This.dma_set();
        });
    },
    dma_set(){
        $(`#${this.dma}`).val(this.address);
    },
    async geocode(request){
        const res = await ymaps.geocode(request);
        this.obj = await res.geoObjects.get(0);
        this.coords = this.obj.geometry.getCoordinates();
    },
    async createMap() {
        const mapContainer = $('#map'),
        bounds = this.obj.properties.get('boundedBy');
        this.state = ymaps.util.bounds.getCenterAndZoom(
            bounds,
            [mapContainer.width(), mapContainer.height()]
        );
        this.state.controls = [];

        if (!this.placemark) {
            this.createPlacemark();
        } 
        this.map.setCenter(this.state.center, this.state.zoom);
        this.placemark.geometry.setCoordinates(this.state.center);
        this.getAddress();
        this.save_coordinate();
    },
    async createMapClick(){
        if (this.placemark) {
            this.placemark.geometry.setCoordinates(this.coords);
        } else {
            this.createPlacemark();
        }
        this.getAddress();
        this.save_coordinate();
    },
    save_coordinate(){
        $("#deliveryModalCoordinates").val(this.coords.join(', '));
    },
    getAddress(){
        this.placemark.properties.set('iconCaption', 'поиск...');
        this.placemark.properties
                .set({
                    iconCaption: [
                        this.obj.getLocalities().length ? this.obj.getLocalities() : this.obj.getAdministrativeAreas(),
                        this.obj.getThoroughfare() || this.obj.getPremise()
                    ].filter(Boolean).join(', '),
                    balloonContent: this.obj.getAddressLine()
                });
        this.address = this.obj.getAddressLine();
    },
    createPlacemark() {
        this.placemark = new ymaps.Placemark(this.coords, {
            iconCaption: 'поиск...'
        }, {
            preset: 'islands#violetDotIconWithCaption',
            draggable: true
        });
        this.map.geoObjects.add(this.placemark);
        this.placemark.events.add('dragend', async () => {
            const coords = this.placemark.geometry.getCoordinates();
            await this.geocode(coords);
            this.coords = coords;
            this.getAddress();
            this.dma_set();
        });
    },
};

ymaps.ready(function(){
    ym.init();
});
