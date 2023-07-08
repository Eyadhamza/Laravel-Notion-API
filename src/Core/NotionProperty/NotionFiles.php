<?php

namespace Pi\Notion\Core\NotionProperty;

use Illuminate\Http\Resources\MissingValue;
use Pi\Notion\Core\Enums\NotionPropertyTypeEnum;
use Pi\Notion\Core\NotionValue\NotionArrayValue;
use Pi\Notion\Core\NotionValue\NotionBlockContent;

class NotionFiles extends BaseNotionProperty
{
    private ?array $files = null;


    protected function buildValue(): NotionBlockContent
    {
        return NotionArrayValue::make([$this->files ?? new MissingValue()])
            ->type('files');
    }

    protected function buildFromResponse(array $response): BaseNotionProperty
    {
        if (empty($response['files'])) {
            return $this;
        }

        $this->files = $response['files'];

        return $this;
    }

    public function setType(): self
    {
        $this->type = NotionPropertyTypeEnum::FILES;

        return $this;
    }

    public function setFiles(?array $files): NotionFiles
    {
        $this->files = $files;
        return $this;
    }

}
