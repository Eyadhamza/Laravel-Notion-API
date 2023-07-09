<?php

namespace Pi\Notion\Core\NotionProperty;

use Pi\Notion\Core\Models\NotionUser;
use Pi\Notion\Core\BlockContent\NotionBlockContent;
use Pi\Notion\Core\BlockContent\NotionEmptyValue;
use Pi\Notion\Enums\NotionPropertyTypeEnum;

class NotionCreatedBy extends BaseNotionProperty
{
    public NotionUser $createdBy;

    protected function buildValue(): NotionBlockContent
    {
        return NotionEmptyValue::make()->setValueType('created_by');
    }

    protected function buildFromResponse(array $response): BaseNotionProperty
    {
        if (empty($response['created_by'])) {
            return $this;
        }
        $this->createdBy = NotionUser::make()
            ->fromResponse($response['created_by']);

        return $this;
    }

    public function setType(): BaseNotionProperty
    {
        $this->type = NotionPropertyTypeEnum::CREATED_BY;

        return $this;
    }
}
