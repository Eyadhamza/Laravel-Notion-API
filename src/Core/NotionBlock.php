<?php


namespace Pi\Notion\Core;


use Illuminate\Support\Collection;
use Pi\Notion\BlockType;
use Pi\Notion\Common\BlockContent;
use Pi\Notion\Common\NotionRichText;
use Pi\Notion\Traits\ThrowsExceptions;

class NotionBlock extends NotionObject
{
    use ThrowsExceptions;

    private string $type;
    private BlockContent|null $blockContent;
    private string $color;
    private Collection $children;

    public function __construct($type = '', $blockContent = null)
    {
        $this->type = $type;
        $this->blockContent = $blockContent;
        $this->children = new Collection();
    }

    public static function build($response): static
    {

        $block = parent::build($response);
        $block->type = $response['type'] ?? null;
        $block->blockContent = new BlockContent($response[$block->type]);
        return $block;
    }

    public static function make(string $type, BlockContent $blockContent = null): self
    {
        return new self($type, $blockContent);
    }

    public static function mapsBlocksToPage(NotionPage $page): Collection
    {

        return $page->getBlocks()->map(function (NotionBlock $block) {
            return array(
                'type' => $block->type,
                $block->type => $block->contentBody(),
            );
        });
    }

    public function get(mixed $id): Collection
    {
        $response = prepareHttp()->get(NotionWorkspace::BLOCK_URL . $id . '/children');

        $this->throwExceptions($response);
        return $this->buildList($response->json());
    }

    public function delete(mixed $id): static
    {
        $response = prepareHttp()->delete(NotionWorkspace::BLOCK_URL . $id);

        $this->throwExceptions($response);
        return $this->build($response->json());
    }

    public function color(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public static function headingOne(string|NotionRichText $body): self
    {

        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::HEADING_1, $body);
    }

    public static function headingTwo(string|NotionRichText $body): self
    {
        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::HEADING_2, $body);
    }

    public static function headingThree(string|NotionRichText $body): self
    {
        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::HEADING_3, $body);
    }

    public static function paragraph(string|NotionRichText $body): self
    {
        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::PARAGRAPH, $body);
    }

    public static function bulletedList(string|NotionRichText $body): self
    {
        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::BULLETED_LIST, $body);
    }

    public static function numberedList(string|NotionRichText $body): self
    {
        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::NUMBERED_LIST, $body);
    }

    public static function toggle(string|NotionRichText $body): self
    {
        $body = is_string($body) ? NotionRichText::make($body) : $body;

        return self::make(BlockType::TOGGLE, $body);
    }

    public static function quote(string $body): self
    {
        return self::make(BlockType::QUOTE, $body);
    }

    public static function callout(string $body): self
    {
        return self::make(BlockType::CALL_OUT, $body);
    }

    public static function divider(): self
    {
        return self::make(BlockType::DIVIDER);
    }

    public static function code(string $body): self
    {
        return self::make(BlockType::CODE, $body);
    }

    public static function childPage(string $body): self
    {
        return self::make(BlockType::CHILD_PAGE, $body);
    }

    public static function embed(string $body): self
    {
        return self::make(BlockType::EMBED, $body);
    }

    public static function image(string $body): self
    {
        return self::make(BlockType::IMAGE, $body);
    }

    public static function video(string $body): self
    {
        return self::make(BlockType::VIDEO, $body);
    }

    public static function file(string $body): self
    {
        return self::make(BlockType::FILE, $body);
    }


    public function addChildren(array $blocks): self
    {
        collect($blocks)->each(function (NotionBlock $block) {
            $this->children->add($block);
        });
        return $this;
    }

    private function contentBody(): array
    {

        return [
            $this->blockContent->getType() => $this->blockContent->getValue(),
            'color' => $this->color ?? 'default',
            'children' => $this->children->map(function (NotionBlock $child) {
                return array(
                   'type' => $child->type,
                   'object' => 'block',
                   $child->type => $child->contentBody()
                );
            })
        ];

    }


}