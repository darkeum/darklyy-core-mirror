<?php

namespace Boot\System\Media\MediaCollections;

use Illuminate\Contracts\Support\Htmlable;
use Boot\System\Media\Conversions\ConversionCollection;
use Boot\System\Media\Conversions\ImageGenerators\Image;
use Boot\System\Media\Conversions\ImageGenerators\ImageGeneratorFactory;
use Boot\System\Media\MediaCollections\Models\Media;

class HtmlableMedia implements Htmlable, \Stringable
{
    protected string $conversionName = '';

    protected array $extraAttributes = [];

    protected string $loadingAttributeValue = '';

    public function __construct(
        protected Media $media
    ) {
    }

    public function attributes(array $attributes): self
    {
        $this->extraAttributes = $attributes;

        return $this;
    }

    public function conversion(string $conversionName): self
    {
        $this->conversionName = $conversionName;

        return $this;
    }

    public function lazy(): self
    {
        $this->loadingAttributeValue = ('lazy');

        return $this;
    }

    public function toHtml()
    {
        $imageGenerator = ImageGeneratorFactory::forMedia($this->media) ?? new Image();

        if (! $imageGenerator->canHandleMime($this->media->mime_type)) {
            return '';
        }

        $attributeString = collect($this->extraAttributes)
            ->map(fn ($value, $name) => $name.'="'.$value.'"')->implode(' ');

        if (strlen($attributeString)) {
            $attributeString = ' '.$attributeString;
        }

        $loadingAttributeValue = config('media.default_loading_attribute_value');

        if ($this->conversionName !== '') {
            $conversionObject = ConversionCollection::createForMedia($this->media)->getByName($this->conversionName);

            $loadingAttributeValue = $conversionObject->getLoadingAttributeValue();
        }

        if ($this->loadingAttributeValue !== '') {
            $loadingAttributeValue = $this->loadingAttributeValue;
        }

        $viewName = 'image';
        $width = '';
        $height = '';

        if ($this->media->hasResponsiveImages($this->conversionName)) {
            $viewName = config('media.responsive_images.use_tiny_placeholders')
                ? 'responsiveImageWithPlaceholder'
                : 'responsiveImage';

            $responsiveImage = $this->media->responsiveImages($this->conversionName)->files->first();

            $width = $responsiveImage->width();
            $height = $responsiveImage->height();
        }

        $media = $this->media;
        $conversion = $this->conversionName;

        return view("media-library::{$viewName}", compact(
            'media',
            'conversion',
            'attributeString',
            'loadingAttributeValue',
            'width',
            'height',
        ))->render();
    }

    public function __toString(): string
    {
        return $this->toHtml();
    }
}
