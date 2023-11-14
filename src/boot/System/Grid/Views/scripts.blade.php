<script type="text/javascript">
    var loadFirst = 0;
    $(window).on("unload", function() {
        saveGrid();
    });
    $("#{{ $data['id'] }}").on("destroy", function() {
        saveGrid();
    })

    function initGrid() {
        $(this).contextMenu('contextMenu', {
            menuStyle: {
                width: "150px"
            },
            bindings: {
                'edit': function(t) {
                    alert("Edit Row Command Selected");
                },
                'add': function(t) {
                    alert("Add Row Command Selected");
                },
                'del': function(t) {
                    removeModal(1);
                }
            },
            onContextMenu: function(event, menu) {
                var rowId = $(event.target).parent("tr").attr("id")
                $("#{{ $data['id'] }}").setSelection(rowId);
                return true;
            }
        });

    }
    var lastSelection;

    function editRow(id) {
        if (id && id !== lastSelection) {
            var grid = $("#{{ $data['id'] }}");
            grid.jqGrid('restoreRow', lastSelection);
            grid.jqGrid('editRow', id, {
                keys: true
            });
            lastSelection = id;
        }
    }

    function loadGrid() {
        if (loadFirst == 0) {
            loadFirst++;
            if ($.jgrid.isGridInStorage("{{ $data['id'] }}", {
                    storageType: 'localStorage'
                })) {
                const options = {
                    useStorage: true,
                    storageType: "localStorage",
                    clearAfterLoad: false,
                    beforeSetGrid: null,
                    afterSetGrid: null,
                    decompression: false,
                    decompressionModule: "LZString",
                    decompressionMethod: "decompressFromUTF16",
                    restoreData: false
                };
                setTimeout(function() {
                    //$.jgrid.loadState("' . $grid_name . '", "", options);
                    //$("#idGrid").clearGridData();
                    //$("#' . $grid_name . '").trigger("reloadGrid");
                    //initGrid();
                    $("#{{ $grid_name }}").jqGrid("bindKeys");
                }, 300);
            } else {
                console.log('grid is not in session storage');
            }
        }
    };


    function saveGrid() {
        if (loadFirst != 0) {
            var options = {
                useStorage: true,
                storageType: "localStorage",
                beforeSetItem: null,
                compression: false,
                compressionModule: 'LZString',
                compressionMethod: 'compressToUTF16',
                debug: false,
                saveData: false
            }
            $.jgrid.saveState("{{ $data['id'] }}", options);
        } else {
            if ($.jgrid.isGridInStorage("{{ $data['id'] }}", {
                    storageType: 'localStorage'
                })) {
                var options = {
                    useStorage: true,
                    storageType: "localStorage",
                    clearAfterLoad: false,
                    beforeSetGrid: null,
                    afterSetGrid: null,
                    decompression: false,
                    decompressionModule: 'LZString',
                    decompressionMethod: 'decompressFromUTF16',
                    restoreData: true
                }
                $.jgrid.loadState("' . $grid_name . '", false, options);
                //$("#idGrid").clearGridData();
                $("#{{ $data['id'] }}").trigger("reloadGrid");
            } else {
                alert('grid is not in session storage');
            }

        }
        loadFirst++;
        console.log(loadFirst);
    };
    let myRow = 30;
    if (Cookies.get("rowNum")) {
        myRow = Cookies.get("rowNum");
    }
    $.extend($.jgrid.defaults, {
        caption: ``,
        hidegrid: false,
        hoverrows: true,
        colMenu: true,
        viewrecords: true,
        scrollOffset: 21,
        width: null,
        shrinkToFit: true,
        autowidth: true, 
        rowNum: myRow,
        rowList: [30, 50, 100],
        height: 490,
        responsive: true,
        treeGrid_bigData: true,
        styleUI: "Bootstrap",
        hidegrid: false,
        cellEdit: false,
        editurl: 'clientArray',
        scrollPopUp: true,
        scrollLeftOffset: "100%",
        viewrecords: true,
        scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
        emptyrecords: "Scroll to bottom to retrieve new page", // the message will be displayed at the bottom 
        storeNavOptions: true,
        pager: "#{{ $data['pager_id'] }}",
        multipleSearch: true,
        loadtext: '',
        postData: {
            "_token": "{{ csrf_token() }}"
        },
        editData: {
            "_token": "{{ csrf_token() }}"
        },
        loadBeforeSend: function(jqXHR) {
            jqXHR.setRequestHeader('X-XSRF-TOKEN', "{{ csrf_token() }}");
        }
    });
    {{-- $.jgrid.defaults. = ''; --}}
    {{-- var opts = {
        ' . $opts . '
    }; --}}


    function dateRangePicker_onChange() {
        var $input = $('#gs_date_view');
        var old_val = $input.val();
        setTimeout(function() {
            if ($input.val() == old_val) {
                $grid[0].triggerToolbar();

            }
        }, 50);
    }
    var pager = "#{{ $data['pager_id'] }}";
    var $grid = $("#{{ $data['id'] }}");
    var ${{ $data['id'] }} = $grid;

    $grid.jqGrid($.extend({!! $options !!}, typeof({{ $data['extend'] }}) == "undefined" ? {} : {{ $data['extend'] }}));
    $grid.jqGrid('extBindEvents');
    @if ($render_filter_toolbar)
        $grid.jqGrid('filterToolbar');
    @endif
    {!! $nav !!}


    {{-- $grid.jqGrid('navGrid', pager); --}}
    {{-- $grid.jqGrid({}); --}}

    {{-- $.extend($.jgrid.defaults, {
    ajaxGridOptions : {
        beforeSend: function(xhr) {
            $("#{{ $data['id'] }}").block();
        },
        complete: function(xhr) {
            $("#{{ $data['id'] }}").unblock();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            $("#{{ $data['id'] }}").unblock();
        }
    }
}); --}}
    var sp = new PerfectScrollbar('.ui-jqgrid-bdiv', {
        suppressScrollX: true,
        wheelPropagation: false,
        minScrollbarLength: 30,
    });
</script>
