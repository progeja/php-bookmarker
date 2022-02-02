<?php

namespace BMParser;

use InvalidArgumentException;

class Bookmarker
{
    private const LINE_META  = 1001;
    private const LINE_TITLE = 1002;
    private const LINE_H1    = 1003;
    private const LINE_DL    = 1004;
    private const LINE_DT_H3 = 1005;
    private const LINE_DT_A  = 1006;

    /**
     * @var string
     */
    private string $srcBookmarks;

    /**
     * @var string[]
     */
    public array $bookmarks;

    public function __construct()
    {
    }

    public function parse(string $bookmarksContent): self
    {
        $this->srcBookmarks = $bookmarksContent;
        $this->bookmarks    = $this->reFormat();

        if (!$this->isNetscapeBookmarks()) {
            throw new InvalidArgumentException('Unknown string content. Not compatible with Nescape bookmarks.');
        }

        return $this;
    }

    /**
     * Tests that first line contains required DocType.
     *
     * @return bool
     */
    private function isNetscapeBookmarks(): bool
    {
        $netscapeHeader = '<!DOCTYPE NETSCAPE-Bookmark-file-1><!-- This is an automatically generated file. It will be read and overwritten. DO NOT EDIT! -->';

        return $this->bookmarks[0] === $netscapeHeader;
    }

    /**
     * Cleans up all unneccesary code
     *
     * @return string
     */
    private function tidyUp(): string
    {
        $bm = trim($this->srcBookmarks);
        $bm = preg_replace('|[\n\r]|Us', '', $bm);
        $bm = preg_replace('|\s+|s', ' ', $bm);

        return preg_replace('|> <|is', '><', $bm);
    }

    /**
     * @return string[]
     */
    private function reFormat(): array
    {
        $ref = $this->tidyUp();
        $ref = preg_replace('#(<(META|TITLE|H1|DL|DT))#', "\n$1", $ref);

        return explode("\n", $ref);
    }

}
