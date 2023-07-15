<?php

namespace Pi\Notion\Core\BlockContent;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Pi\Notion\Enums\NotionBlockContentTypeEnum;
use Pi\Notion\Enums\NotionBlockTypeEnum;
use Pi\Notion\Enums\NotionPropertyTypeEnum;
use Pi\Notion\Traits\HasResource;

abstract class NotionContent
{
    use HasResource;

    public JsonResource $resource;
    protected mixed $value = null;
    protected bool $isNested = false;
    protected NotionBlockTypeEnum|NotionPropertyTypeEnum $blockType;
    protected NotionBlockContentTypeEnum $contentType;

    public function __construct(NotionBlockTypeEnum|NotionPropertyTypeEnum $valueType, mixed $value = null)
    {
        $this->blockType = $valueType;
        $this->value = $value;
        $this->setContentType();
    }

    public static function make(NotionBlockTypeEnum|NotionPropertyTypeEnum $valueType, mixed $value = null): static|NotionEmptyValue
    {
        if (!$value) {
             return new NotionEmptyValue($valueType, $value);
        }

        if (is_array($value) && self::areAllMissingValues($value)) {
            return new NotionEmptyValue($valueType, $value);
        }

        return new static($valueType, $value);
    }

    abstract public static function fromResponse(array $response): static;

    public function resource()
    {
        $this->value = $this->toArrayableValue();

        $this->resource = JsonResource::make($this->value);

        return $this->resource->resolve();
    }
    private static function areAllMissingValues(array $value): bool
    {
        foreach ($value as $item) {
            if (!$item instanceof MissingValue) {
                return false;
            }
        }

        return true;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getContentType(): NotionBlockContentTypeEnum
    {
        return $this->contentType;
    }

    public function setBlockType(NotionBlockTypeEnum|NotionPropertyTypeEnum $blockType): self
    {
        $this->blockType = $blockType;
        return $this;
    }

    public function isNested(): self
    {
        $this->isNested = true;
        return $this;
    }

    public abstract function toArrayableValue(): array;
    protected abstract function setContentType(): self;

}
