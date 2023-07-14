<?php

namespace Pi\Notion\Core\Models;

use Pi\Notion\Core\BlockContent\NotionRichText;
use Pi\Notion\Core\NotionClient;
use Pi\Notion\Core\Query\NotionPaginator;
use Pi\Notion\Core\RequestBuilders\CreateNotionCommentRequestBuilder;

class NotionComment extends NotionObject
{
    const COMMENTS_URL =  NotionClient::BASE_URL . '/comments/';

    private ?string $discussionId = null;
    private NotionRichText $content;
    private string $parentId;


    public function fromResponse($response): self
    {
        parent::fromResponse($response);
        $this->discussionId = $response['discussion_id'];
        $this->content = NotionRichText::build($response['rich_text']);
        return $this;
    }

    public function create(): self
    {
        $body = CreateNotionCommentRequestBuilder::make()
            ->setDiscussionId($this->discussionId)
            ->setParentId($this->parentId)
            ->setContent($this->content)
            ->build();

        $response = NotionClient::make()
            ->post(self::COMMENTS_URL, $body);

        return $this->fromResponse($response->json());
    }

    public function findAll(int $pageSize = 50): NotionPaginator
    {
        return NotionPaginator::make(NotionComment::class)
            ->setUrl(self::COMMENTS_URL)
            ->setMethod('get')
            ->setBody(['block_id' => $this->id])
            ->setPageSize($pageSize)
            ->paginate();
    }


    public function setDiscussionId(string $discussionId): self
    {
        $this->discussionId = $discussionId;
        return $this;
    }

    public function setParentId(string $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function setContent(string|NotionRichText $text): static
    {
        $this->content = is_string($text) ? NotionRichText::make($text) : $text;

        return $this;
    }
}
