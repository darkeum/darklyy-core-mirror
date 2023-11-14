$(function(){
    $extendedSort.init();
});

class ExtendedSort_v5 extends ExtendedSort{
    constructor(){
        super();
        this.modal = new bootstrap.Modal(document.getElementById("extendedSortModal"));
    }
    modal_show(){
        this.modal.show();
    }
    modal_hide(){
        this.modal.hide();
    }
}

$extendedSort = new ExtendedSort_v5;