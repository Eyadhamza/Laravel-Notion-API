<?php


namespace Pi\Notion\Core;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Pi\Notion\Exceptions\NotionException;
use Pi\Notion\NotionClient;
use Pi\Notion\Traits\HandleBlocks;
use Pi\Notion\Traits\HandleProperties;

class NotionPage extends NotionObject
{
    use HandleProperties, HandleBlocks;


    private string $notionDatabaseId;
    protected Collection $blocks;
    protected Collection $properties;


    public function __construct($id = '')
    {
        $this->id = $id;
        $this->blocks = new Collection();
        $this->properties = new Collection();
    }

    public static function build($response): static
    {

        $page = parent::build($response);
        $page->lastEditedBy = new NotionUser($response['last_edited_by']['id'] ?? '') ?? null;
        $page->url = $response['url'] ?? null;
        $page->icon = $response['icon'] ?? null;
        $page->cover = $response['cover'] ?? null;

        $page->properties = new Collection();
        $page->buildProperties($response);
        return $page;
    }

    public function get(): self
    {
        $response = NotionClient::request('get', $this->getUrl());

        return $this->build($response);
    }

    public function getProperty(string $name)
    {
        $property = $this
            ->properties
            ->filter
            ->ofName($name)
            ->firstOrFail();
        $response = NotionClient::request('get', $this->propertyUrl($property->getId()));


        $property->setValues($response[$property->getType()] ?? []);

        if ($property->isPaginated()) {
            NotionProperty::buildList($response);
        }

        return $property;
    }

    public function getWithContent(): NotionPagination
    {
        return (new NotionBlock)->getChildren($this->id);
    }

    public static function find($id): self
    {
        return (new NotionPage($id))->get();
    }

    public static function findContent($id): NotionPagination
    {
        return (new NotionPage($id))->getWithContent();
    }

    public function create(): self
    {
        $response = NotionClient::request('post', NotionWorkspace::PAGE_URL, [
            'parent' => array('database_id' => $this->getDatabaseId()),
            'properties' => NotionProperty::mapsProperties($this),
            'children' => NotionBlock::mapsBlocksToPage($this)
        ]);
        return $this->build($response);
    }

    public function update(): self
    {
        $response = NotionClient::request('patch', $this->getUrl(), [
            'properties' => NotionProperty::mapsProperties($this),
        ]);
        return $this->build($response);
    }

    public function delete(): NotionBlock
    {
        return (new NotionBlock)
            ->setId($this->id)
            ->delete();
    }

    public function setDatabaseId(string $notionDatabaseId): void
    {
        $this->notionDatabaseId = $notionDatabaseId;
    }

    private function getUrl(): string
    {
        return NotionWorkspace::PAGE_URL . $this->id;
    }


    private function getDatabaseId()
    {
        return $this->notionDatabaseId;
    }

    private function propertyUrl($id)
    {
        return $this->getUrl() . '/properties/' . $id;
    }

}
