<?php

namespace Pi\Notion\Tests\Featured;

use Pi\Notion\Common\NotionRichText;
use Pi\Notion\Core\NotionComment;
use Pi\Notion\Core\NotionUser;
use Pi\Notion\Tests\TestCase;

class ManageCommentTest extends TestCase
{
    /** @test */
    public function it_returns_all_comments()
    {
        $comments = NotionComment::findAll('0b036890391f417cbac775e8b0bba680');

        $this->assertCount(3, $comments->getResults());
        // https://www.notion.so/Eyad-Hamza-?d=aca6764ef26546c0832d444ead358c26
    }
    /** @test */
    public function it_can_create_a_comment()
    {
        $comment = new NotionComment();

        $comment = $comment
            ->setDiscussionId('ac803deb7b064cca83067c67914b02b4')
            ->setContent(NotionRichText::make('This is a comment')
                ->color('red')
                ->bold()
                ->italic()
            )
            ->create();
        $comment = $comment
            ->setParentId('a270ab4fa945449f9284b180234b00c3')
            ->setContent('This is a comment')
            ->create();
        $this->assertInstanceOf(NotionComment::class, $comment);
    }

}
