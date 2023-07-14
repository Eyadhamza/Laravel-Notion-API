<?php

namespace Pi\Notion\Core\NotionProperty;

use Illuminate\Http\Resources\MissingValue;
use Pi\Notion\Core\BlockContent\NotionArrayValue;
use Pi\Notion\Core\BlockContent\NotionContent;
use Pi\Notion\Core\BlockContent\NotionSimpleValue;
use Pi\Notion\Enums\NotionPropertyTypeEnum;
use Pi\Notion\Enums\NotionNumberFormatEnum;

class NotionNumber extends BaseNotionProperty
{
    private ?int $number = null;
    private ?NotionNumberFormatEnum $format = null;

    public function buildContent(): self
    {
        if (!$this->number) {
            $this->blockContent = NotionArrayValue::make($this->type, [
                'number' => new MissingValue(),
                'format' => new MissingValue(),
            ]);

            return $this;
        }

        $this->blockContent = NotionSimpleValue::make([
            'value' => $this->number,
        ])
            ->setValueType($this->type);

        return $this;
    }


    public function setType(): BaseNotionProperty
    {
        $this->type = NotionPropertyTypeEnum::NUMBER;

        return $this;
    }

    public function setNumber(?int $number): NotionNumber
    {
        $this->number = $number;
        return $this;
    }

    public function setFormat(?NotionNumberFormatEnum $format): NotionNumber
    {
        $this->format = $format;
        return $this;
    }


    public function mapToResource(): array
    {
        return [
            'number' => $this->number,
            'format' => $this->format
        ];
    }
}

