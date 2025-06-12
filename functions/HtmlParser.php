<?php
class HtmlParser {
    private $openTags = [];  
    private $results = [];
    private $openTagHandler = null;
    private $closeTagHandler = null;
    private $textHandler = null;

    public function onOpenTag(callable $handler): void {
        $this->openTagHandler = $handler;
    }
    public function onCloseTag(callable $handler): void {
        $this->closeTagHandler = $handler;
    }

    public function onText(callable $handler): void {
        $this->textHandler = $handler;
    }
    public function parse(string $html): void {
        preg_match_all('/(<(\/?)(\w+)(?:[^>]*)>)|([^<]+)/', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (!empty($match[1])) {
                $isClosing = !empty($match[2]);
                $tagName = strtolower($match[3]);
                $this->handleTag($tagName, $isClosing);
            } elseif (!empty($match[4])) {
                $this->handleText(trim($match[4]));
            }
        }
    }
    private function handleTag(string $tag, bool $isClosing): void {
        if ($isClosing) {
            array_pop($this->openTags);
            if ($this->closeTagHandler) {
                call_user_func($this->closeTagHandler, $tag, count($this->openTags));
            }
        } else {
            array_push($this->openTags, $tag);
            if ($this->openTagHandler) {
                call_user_func($this->openTagHandler, $tag, count($this->openTags) - 1);
            }
        }
    }
    private function handleText(string $text): void {
        if ($text !== '' && $this->textHandler) {
            $currentLevel = count($this->openTags);
            call_user_func($this->textHandler, $text, $currentLevel);
        }
    }
    public function getResults(): array {
        return $this->results;
    }
}