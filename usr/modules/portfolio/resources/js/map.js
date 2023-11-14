const ym = {
    init(){
        this.mapContainer = $('#map');
        this.dma = "address";
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
        $("#coord").val(this.coords.join(', '));
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
