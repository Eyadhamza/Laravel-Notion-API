<?php

namespace Pi\Notion\Core\NotionProperty;

use Illuminate\Http\Resources\MissingValue;
use Pi\Notion\Core\BlockContent\NotionArrayValue;
use Pi\Notion\Core\BlockContent\NotionContent;
use Pi\Notion\Core\BlockContent\NotionFile;
use Pi\Notion\Enums\NotionPropertyTypeEnum;

class NotionFiles extends BaseNotionProperty
{

    public ?array $files = [];

    protected function buildFromResponse(array $response): BaseNotionProperty
    {
        if (empty($response['files'])) {
            return $this;
        }

        $this->rawValue = $response['files'];

        return $this;
    }

    public function setType(): self
    {
        $this->type = NotionPropertyTypeEnum::FILES;

        return $this;
    }

    public function setFiles(?array $files): NotionFiles
    {
        $this->files = collect($files)
            ->map(fn(NotionFile $file) => $file
                ->buildResource()
                ->resource
                ->resolve()
            )
            ->all();

        return $this;
    }

    public function mapToResource(): array
    {
        return $this->files ?? [];
    }
}
