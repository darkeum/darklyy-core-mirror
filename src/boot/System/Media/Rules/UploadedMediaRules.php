<?php

namespace Boot\System\Media\Rules;

use Illuminate\Contracts\Validation\Rule;
use Boot\System\Media\Rules\GroupRules\MaxItemsRule;
use Boot\System\Media\Rules\GroupRules\MaxTotalSizeInKbRule;
use Boot\System\Media\Rules\GroupRules\MinItemsRule;
use Boot\System\Media\Rules\GroupRules\MinTotalSizeInKbRule;
use Boot\System\Media\Rules\ItemRules\AttributeRule;
use Boot\System\Media\Rules\ItemRules\DimensionsRule;
use Boot\System\Media\Rules\ItemRules\ExtensionRule;
use Boot\System\Media\Rules\ItemRules\HeightBetweenRule;
use Boot\System\Media\Rules\ItemRules\MaxItemSizeInKbRule;
use Boot\System\Media\Rules\ItemRules\MimeTypeRule;
use Boot\System\Media\Rules\ItemRules\MinItemSizeInKbRule;
use Boot\System\Media\Rules\ItemRules\WidthBetweenRule;

class UploadedMediaRules implements Rule
{
    public array $groupRules = [];

    public array $itemRules = [];

    public function minItems(int $numberOfItems): self
    {
        $this->groupRules[] = new MinItemsRule($numberOfItems);

        return $this;
    }

    public function maxItems(int $numberOfItems): self
    {
        $this->groupRules[] = new MaxItemsRule($numberOfItems);

        return $this;
    }

    public function maxTotalSizeInKb(int $maxTotalSizeInKb): self
    {
        $this->groupRules[] = new MaxTotalSizeInKbRule($maxTotalSizeInKb);

        return $this;
    }

    public function minTotalSizeInKb(int $minTotalSizeInKb): self
    {
        $this->groupRules[] = new MinTotalSizeInKbRule($minTotalSizeInKb);

        return $this;
    }

    public function maxItemSizeInKb(int $maxSizeInKb): self
    {
        $this->itemRules[] = new MaxItemSizeInKbRule($maxSizeInKb);

        return $this;
    }

    public function minSizeInKb(int $minSizeInKb): self
    {
        $this->itemRules[] = new MinItemSizeInKbRule($minSizeInKb);

        return $this;
    }

    /** @var string|array */
    public function mime($mimes): self
    {
        $this->itemRules[] = new MimeTypeRule($mimes);

        return $this;
    }

    /**
     * @var string|array
     */
    public function extension($extensions): self
    {
        $this->itemRules[] = new ExtensionRule($extensions);

        return $this;
    }

    public function itemName($rules): self
    {
        return $this->attribute('name', $rules);
    }

    /**
     * @param string $attribute
     * @param array|string $rules
     */
    public function attribute(string $attribute, $rules): self
    {
        $this->itemRules[] = new AttributeRule($attribute, $rules);

        return $this;
    }

    public function customProperty(string $customPropertyName, $rules): self
    {
        $customPropertyName = "custom_properties.{$customPropertyName}";

        $this->itemRules[] = new AttributeRule($customPropertyName, $rules);

        return $this;
    }

    public function dimensions(int $width, int $height): self
    {
        $this->itemRules[] = new DimensionsRule($width, $height);

        return $this;
    }

    public function width(int $width): self
    {
        $this->itemRules[] = new DimensionsRule($width, 0);

        return $this;
    }

    public function height(int $height): self
    {
        $this->itemRules[] = new DimensionsRule(0, $height);

        return $this;
    }

    public function widthBetween(int $minWidth, int $maxWidth): self
    {
        $this->itemRules[] = new WidthBetweenRule($minWidth, $maxWidth);

        return $this;
    }

    public function heightBetween(int $minHeight, int $maxHeight): self
    {
        $this->itemRules[] = new HeightBetweenRule($minHeight, $maxHeight);

        return $this;
    }

    public function passes($attribute, $value)
    {
        // this page has been left intentionally blank
    }

    public function message()
    {
        // this page has been left intentionally blank
    }
}
