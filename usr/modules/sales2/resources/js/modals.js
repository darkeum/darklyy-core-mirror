function getModalData() {
    return {
        statusModal: {
            modal: null,
            data: null,
            load: false,
        },
        placeOrderModal: {
            modal: null,
            list: [false, false, false, false, ]
        },
        printModal: {
            modal: null,
        },
        commentModal: {
            modal: null,
            messages: [],
            message: null,
        },
        messageModal: {
            modal: null,
            data: [],
            step: 1,
        },
        usersModal: {
            modal: null,
            data: [],
        },
        assemblyModal: {
            modal: null,
            data: [],
        },
        changeOidModal: {
            modal: null,
        },
        changeUidModal: {
            modal: null,
        },
        changeUidMaintenanceModal: {
            modal: null,
        },
        changeObjectModal: {
            modal: null,
        },
        contractModal: {
            modal: null,
        },
        specificationModal: {
            modal: null,
            delivery: 1,
        },
        payModal: {
            modal: null,
            template: null,
        },
        viewModal: {
            modal: null,
            title: null,
            template: null,
        },
        reserveModal: {
            modal: null,
            title: null,
            action: null,
            all: false,
            org: {},
            id: [],
        },
        organizationModal: {
            modal: null,
        },
        filesModal: {
            modal: null,
            files: [],
        },
    }
}

function modalsInit(This) {
    This.modals.statusModal.modal = new bootstrap.Modal(document.getElementById('statusModal'));
    This.modals.placeOrderModal.modal = new bootstrap.Modal(document.getElementById('placeOrderModal'));
    This.modals.printModal.modal = new bootstrap.Modal(document.getElementById('printModal'));
    This.modals.commentModal.modal = new bootstrap.Modal(document.getElementById('commentModal'));
    This.modals.messageModal.modal = new bootstrap.Modal(document.getElementById('messageModal'));
    This.modals.usersModal.modal = new bootstrap.Modal(document.getElementById('usersModal'));
    This.modals.assemblyModal.modal = new bootstrap.Modal(document.getElementById('assemblyModal'));
    This.modals.changeOidModal.modal = new bootstrap.Modal(document.getElementById('changeOidModal'));
    This.modals.changeUidModal.modal = new bootstrap.Modal(document.getElementById('changeUidModal'));
    This.modals.changeUidMaintenanceModal.modal = new bootstrap.Modal(document.getElementById('changeUidMaintenanceModal'));
    This.modals.changeObjectModal.modal = new bootstrap.Modal(document.getElementById('changeObjectModal'));
    This.modals.contractModal.modal = new bootstrap.Modal(document.getElementById('contractModal'));
    This.modals.contractModal.elements = {
        form: document.getElementById("contractModalForm"),
        body: document.getElementById("contractModalBody"),
        save: document.getElementById("contractModalSave"),
    };
    This.modals.specificationModal.modal = new bootstrap.Modal(document.getElementById('specificationModal'));
    This.modals.payModal.modal = new bootstrap.Modal(document.getElementById('payModal'));
    This.modals.viewModal.modal = new bootstrap.Modal(document.getElementById('viewModal'));
    This.modals.reserveModal.modal = new bootstrap.Modal(document.getElementById('reserveModal'));
    This.modals.organizationModal.modal = new bootstrap.Modal(document.getElementById('organizationModal'));
    This.modals.filesModal.modal = new bootstrap.Modal(document.getElementById('filesModal'));
}

function dis_off_reestr() {
    document.getElementById("contractModalSave").disabled = false;
}