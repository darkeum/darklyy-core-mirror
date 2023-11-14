<div class="card" >
    <div class="card-header">
      <h4 class="card-title">Файлы заявки</h4>
      
      <span id="btnAddRightFile_2" class="badge bg-success" style="cursor: pointer">
        Добавить
      </span>

    </div>
    <div class="card-body" style="overflow:hidden">

      @if($files->isNotEmpty())

        <div id="jstree-basic" style="word-break: break-all;" class="pe-2">        
              <ul>
                @foreach ($files as $file)
                  <li  data-jstree='{"icon" : "fab fa-solid fa-file"}'><a onclick="scrollToFile({{$file['id']}})"> {{ $file['name'] }} </a></li>
                @endforeach  
              </ul>
        </div>

        @else

          <span class="badge bg-danger">
            Отсутствуют
          </span>

        @endif
    </div>
</div>

@push('styles')
    <style>
      .jstree-anchor {
          white-space: break-spaces !important;
          height: auto !important;
      }
    </style>
@endpush

@push('scripts')
    <script data-title="Module - Requests">

        window.addEventListener('treeUpdate', function (e) {
          let tree = $('#jstree-basic').jstree();
        });

        $('#btnAddRightFile_2').on('click', function() {
            $('#fileRight').trigger('click');
        });

        function scrollToFile(fileId)
        {
          let href = $('#file_'+fileId);

          $("html, body").animate({
              scrollTop: $(href).position().top
          }, {
              duration: 0,   // по умолчанию «400»
              easing: "linear" // по умолчанию «swing»
          });

          $(href).effect("highlight", {color: '#8CAEC9'}, 5000);
        }

    </script>
@endpush
