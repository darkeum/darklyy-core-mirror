<?php

namespace Boot\System\Media\Http\Components;

use Illuminate\View\Component;

class MediaLibraryAttachmentComponent extends Component
{
    public string $name;
    public string $rules;
    public bool $multiple;
    public bool $editableName;

    public array $media;
    public ?int $maxItems;

    public ?string $componentView = null;
    public ?string $listView;
    public ?string $itemView;
    public ?string $propertiesView = null;

    public function __construct(
        string $name,
        string $rules = '',
        $multiple = false,
        $editableName = false,
        ?int $maxItems = null,
        ?string $view = null,
        ?string $listView = null,
        ?string $itemView = null,
        ?string $propertiesView = null
    ) {
        $this->name = $name;
        $this->rules = $rules;
        $this->multiple = $multiple;
        $this->editableName = $editableName;
        $this->maxItems = $maxItems;

        $this->media = old($name) ?? [];

        $this->componentView = $view;
        $this->listView = $listView;
        $this->itemView = $itemView;
        $this->propertiesView = $propertiesView ?? 'livewire.partials.attachment.properties';
    }

    public function render()
    {
        return view('components.media-library-attachment');
    }

    public function determineListViewName(): string
    {
        if (! is_null($this->listView)) {
            return $this->listView;
        }

        return 'livewire.partials.attachment.list';
    }

    public function determineItemViewName(): string
    {
        if (! is_null($this->itemView)) {
            return $this->itemView;
        }

        return 'livewire.partials.attachment.item';
    }

    public function determineMaxItems(): ?int
    {
        return $this->multiple
            ? $this->maxItems
            : 1;
    }
}
