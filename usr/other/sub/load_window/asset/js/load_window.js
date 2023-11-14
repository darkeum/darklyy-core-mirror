class LW{
    constructor(){
        this.id = "load_window_overlay";
        this.cssId = 'load_window_css';
        this.animate = this._appendDefaultAnimate;
    }
    on(){
        const overlay_has = $("#"+this.id).length;
        if(overlay_has == 0){
            const link = this._createLink();
            document.head.appendChild(link);
            const overlay = this._createOverlay();
            const overlay_animate = this.animate(overlay);
            document.body.append(overlay_animate);
        }
    }
    off(){
        const overlay = $("#"+this.id);
        overlay.fadeOut("slow", function(){
            overlay.remove();
            const link = document.getElementById(this.cssId);
            if(link){
                link.remove();
            }
        });
        
    }
    _createOverlay(){
        //create element
        const overlay = document.createElement("div");
        overlay.setAttribute("id", this.id);
        return overlay;
    }
    _createLink(){
        const link  = document.createElement('link');
        link.id   = this.cssId;
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = '/usr/other/sub/load_window/asset/css/load_window.css';
        link.media = 'all';
        return link;
    }
    _appendDefaultAnimate(overlay){
        const anim = `
        <div class="banter-container">
            <div class="banter-loader">
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
              <div class="banter-loader__box"></div>
            </div>
            <div class="banter-loader_text">Загрузка...</div>
        </div>
            
        `;
        overlay.innerHTML = anim;
        return overlay;
    }
}