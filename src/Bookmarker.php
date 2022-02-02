<?php

namespace BMParser;

use InvalidArgumentException;

class Bookmarker
{
    private const LINE_OTHER = 1000;
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
    private array $bookmarkMeta;

    public function __construct()
    {
    }

    public function parse(string $bookmarksContent): self
    {
        $this->srcBookmarks = $bookmarksContent;
        $this->bookmarkMeta = $this->reFormat();

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

        return $this->bookmarkMeta[0] === $netscapeHeader;
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
     * @param string $lineString
     *
     * @return int
     */
    private function getLineType(string $lineString): int
    {
        if (str_starts_with($lineString, '<META')) {
            return self::LINE_META;
        }
        if (str_starts_with($lineString, '<TITLE')) {
            return self::LINE_TITLE;
        }
        if (str_starts_with($lineString, '<H1')) {
            return self::LINE_H1;
        }
        if (str_starts_with($lineString, '<DL')) {
            return self::LINE_DL;
        }
        if (str_starts_with($lineString, '<DT><H3')) {
            return self::LINE_DT_H3;
        }
        if (str_starts_with($lineString, '<DT><A ')) {
            return self::LINE_DT_A;
        }

        return self::LINE_OTHER;
    }

    /**
     * @param string $markupLine
     *
     * @return string[]
     */
    private function markupLineParser(string $markupLine): array
    {
        // markup parser code here ...
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
