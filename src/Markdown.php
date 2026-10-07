<?php

declare(strict_types=1);

namespace Phpugdd\Website;

/**
 * Deliberately small Markdown subset: headings, paragraphs, lists, blockquotes, rules, fenced code,
 * inline emphasis/code/links/images. Lines starting with "<" open a raw HTML block until the next blank line.
 * Content comes from the repository itself, so inline HTML is trusted and passed through.
 */
final class Markdown
{
    private const LIST_ITEM = '/^(\s*)([-*]|\d+\.)\s+(.*)$/';

    public function toHtml(string $markdown): string
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $markdown));

        return implode("\n", $this->blocks($lines));
    }

    public function inline(string $text): string
    {
        $codeSpans = [];
        $text = preg_replace_callback('/`([^`]+)`/', static function (array $m) use (&$codeSpans): string {
            $codeSpans[] = '<code>' . htmlspecialchars($m[1], ENT_NOQUOTES) . '</code>';

            return "\x1A" . (count($codeSpans) - 1) . "\x1A";
        }, $text);

        $text = preg_replace('/!\[([^\]]*)\]\(([^)\s]+)\)/', '<img src="$2" alt="$1" loading="lazy">', $text);
        $text = preg_replace('/\[([^\]]+)\]\(([^)\s]+)\)/', '<a href="$2">$1</a>', $text);
        $text = preg_replace('/<(https?:\/\/[^>\s]+)>/', '<a href="$1">$1</a>', $text);
        $text = preg_replace('/<([^@>\s]+@[^@>\s]+\.[a-z]+)>/i', '<a href="mailto:$1">$1</a>', $text);
        $text = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/(?<![*\w])\*(?!\s)(.+?)(?<!\s)\*(?![*\w])/', '<em>$1</em>', $text);
        $text = preg_replace('/(?<!\w)_(?!\s)(.+?)(?<!\s)_(?!\w)/', '<em>$1</em>', $text);
        $text = preg_replace('/( {2,}|\\\\)\n/', "<br>\n", $text);

        return preg_replace_callback("/\x1A(\d+)\x1A/", static fn (array $m): string => $codeSpans[(int) $m[1]], $text);
    }

    /**
     * @param list<string> $lines
     * @return list<string>
     */
    private function blocks(array $lines): array
    {
        $html = [];
        $i = 0;
        $count = count($lines);

        while ($i < $count) {
            $line = $lines[$i];
            $trimmed = trim($line);

            if ($trimmed === '') {
                $i++;
                continue;
            }

            [$block, $i] = match (true) {
                str_starts_with($trimmed, '```') => $this->fencedCode($lines, $i),
                (bool) preg_match('/^<\/?[a-z][a-z0-9-]*[\s>\/]/i', $trimmed) => $this->rawHtml($lines, $i),
                (bool) preg_match('/^(#{1,6})\s+(.+?)\s*#*$/', $trimmed) => [$this->heading($trimmed), $i + 1],
                (bool) preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trimmed) => ['<hr>', $i + 1],
                str_starts_with($trimmed, '>') => $this->blockquote($lines, $i),
                (bool) preg_match(self::LIST_ITEM, $line) => $this->list($lines, $i),
                default => $this->paragraph($lines, $i),
            };
            $html[] = $block;
        }

        return $html;
    }

    private function heading(string $line): string
    {
        preg_match('/^(#{1,6})\s+(.+?)\s*#*$/', $line, $m);
        $level = strlen($m[1]);
        $id = self::slug(strip_tags($this->inline($m[2])));

        return sprintf('<h%d id="%s">%s</h%1$d>', $level, $id, $this->inline($m[2]));
    }

    /** @return array{string, int} */
    private function fencedCode(array $lines, int $i): array
    {
        $language = trim(substr(trim($lines[$i]), 3));
        $code = [];
        $i++;
        while ($i < count($lines) && !str_starts_with(trim($lines[$i]), '```')) {
            $code[] = $lines[$i++];
        }
        $class = $language === '' ? '' : sprintf(' class="language-%s"', htmlspecialchars($language));

        return [sprintf('<pre><code%s>%s</code></pre>', $class, htmlspecialchars(implode("\n", $code), ENT_NOQUOTES)), $i + 1];
    }

    /** @return array{string, int} */
    private function rawHtml(array $lines, int $i): array
    {
        $block = [];
        while ($i < count($lines) && trim($lines[$i]) !== '') {
            $block[] = $lines[$i++];
        }

        return [implode("\n", $block), $i];
    }

    /** @return array{string, int} */
    private function blockquote(array $lines, int $i): array
    {
        $inner = [];
        while ($i < count($lines) && str_starts_with(trim($lines[$i]), '>')) {
            $inner[] = preg_replace('/^\s*>\s?/', '', $lines[$i++]);
        }

        return ['<blockquote>' . implode("\n", $this->blocks($inner)) . '</blockquote>', $i];
    }

    /** @return array{string, int} */
    private function paragraph(array $lines, int $i): array
    {
        $text = [];
        while ($i < count($lines) && $this->continuesParagraph($lines[$i])) {
            $text[] = $lines[$i++];
        }

        return ['<p>' . $this->inline(trim(implode("\n", $text))) . '</p>', $i];
    }

    private function continuesParagraph(string $line): bool
    {
        $trimmed = trim($line);

        return $trimmed !== ''
            && !preg_match('/^(#{1,6}\s|```|>|(-{3,}|\*{3,})$)/', $trimmed)
            && !preg_match(self::LIST_ITEM, $line);
    }

    /** @return array{string, int} */
    private function list(array $lines, int $i): array
    {
        preg_match(self::LIST_ITEM, $lines[$i], $first);
        $indent = strlen($first[1]);
        $tag = ctype_digit($first[2][0]) ? 'ol' : 'ul';
        $items = [];

        while ($i < count($lines)) {
            $line = $lines[$i];
            if (preg_match(self::LIST_ITEM, $line, $m) && strlen($m[1]) === $indent) {
                $items[] = [$m[3]];
                $i++;
                continue;
            }
            $isContinuation = trim($line) !== '' && $this->indentOf($line) > $indent;
            $isLooseGap = trim($line) === '' && isset($lines[$i + 1]) && $this->indentOf($lines[$i + 1]) > $indent && trim($lines[$i + 1]) !== '';
            if ($items === [] || !($isContinuation || $isLooseGap)) {
                break;
            }
            $items[count($items) - 1][] = substr($line, min($this->indentOf($line), $indent + 2));
            $i++;
        }

        $html = array_map(fn (array $item): string => '<li>' . $this->listItem($item) . '</li>', $items);

        return [sprintf('<%1$s>%2$s</%1$s>', $tag, implode('', $html)), $i];
    }

    /** @param list<string> $lines */
    private function listItem(array $lines): string
    {
        $nestedAt = null;
        foreach ($lines as $index => $line) {
            if ($index > 0 && (preg_match(self::LIST_ITEM, $line) || trim($line) === '')) {
                $nestedAt = $index;
                break;
            }
        }
        if ($nestedAt === null) {
            return $this->inline(trim(implode("\n", $lines)));
        }

        $head = $this->inline(trim(implode("\n", array_slice($lines, 0, $nestedAt))));

        return $head . implode('', $this->blocks(array_slice($lines, $nestedAt)));
    }

    private function indentOf(string $line): int
    {
        return strlen($line) - strlen(ltrim($line));
    }

    public static function slug(string $text): string
    {
        $text = mb_strtolower(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = strtr($text, ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss']);

        return trim((string) preg_replace('/[^a-z0-9]+/', '-', $text), '-');
    }
}
