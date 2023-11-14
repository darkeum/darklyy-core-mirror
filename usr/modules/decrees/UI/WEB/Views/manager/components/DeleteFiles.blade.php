<div id="slugHelp" class="form-text">
    
    @if($fid != null)
        <a target="_blank" title='Скачать' 
        href="{{ route('admin.decrees.getFile', [$unit->id,$mode]) }}"
        class="text-info">{{ $fileName }} 
        </a>
    @else
        <a class="text-danger">{{ $fileName }}</a>
    @endif
    
</div>

