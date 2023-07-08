<?php

namespace Pi\Notion\Core\NotionProperty;

use Illuminate\Http\Resources\MissingValue;
use Pi\Notion\Core\Enums\NotionPropertyTypeEnum;
use Pi\Notion\Core\NotionValue\NotionArrayValue;
use Pi\Notion\Core\NotionValue\NotionBlockContent;

class NotionPeople extends BaseNotionProperty
{
    private ?array $people = null;

    protected function buildValue(): NotionBlockContent
    {
        return NotionArrayValue::make([$this->people ?? new MissingValue()])
            ->type('people');
    }

    protected function buildFromResponse(array $response): BaseNotionProperty
    {
        if (empty($response['people'])) {
            return $this;
        }

        $this->people = $response['people'];

        return $this;
    }

    public function setType(): BaseNotionProperty
    {
        $this->type = NotionPropertyTypeEnum::PEOPLE;

        return $this;
    }

    public function setPeople(?array $people): NotionPeople
    {
        $this->people = $people;
        return $this;
    }

}

