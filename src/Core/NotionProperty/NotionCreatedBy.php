<?php

namespace Pi\Notion\Core\NotionProperty;

use Pi\Notion\Core\Enums\NotionPropertyTypeEnum;
use Pi\Notion\Core\NotionValue\NotionBlockContent;
use Pi\Notion\Core\NotionValue\NotionEmptyValue;

class NotionCreatedBy extends BaseNotionProperty
{
    protected function buildValue(): NotionBlockContent
    {
        return NotionEmptyValue::make()->type('created_by');
    }

    protected function buildFromResponse(array $response): BaseNotionProperty
    {
        if (empty($response['created_by'])) {
            return $this;
        }
        $this->createdTime = $response['created_by'];
        return $this;
    }

    public function setType(): BaseNotionProperty
    {
        $this->type = NotionPropertyTypeEnum::CREATED_BY;

        return $this;
    }
}
