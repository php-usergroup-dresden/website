<?php

declare(strict_types=1);

use Phpugdd\Website\Markdown;

$md = new Markdown();

return [
    'headings get a slug id' => static fn () => assertSame(
        '<h2 id="ueber-uns-mehr">Über uns &amp; mehr</h2>',
        $md->toHtml('## Über uns &amp; mehr'),
    ),
    'paragraphs are separated by blank lines' => static fn () => assertSame(
        "<p>Eins\nzwei</p>\n<p>Drei</p>",
        $md->toHtml("Eins\nzwei\n\nDrei"),
    ),
    'inline formatting' => static fn () => assertSame(
        '<p><strong>fett</strong>, <em>kursiv</em>, <code>&lt;?php</code> und <a href="/events.html">Link</a></p>',
        $md->toHtml('**fett**, *kursiv*, `<?php` und [Link](/events.html)'),
    ),
    'underscores inside words stay untouched' => static fn () => assertSame(
        '<p><img src="/images/a_b_c.jpg" alt="Bild" loading="lazy"></p>',
        $md->toHtml('![Bild](/images/a_b_c.jpg)'),
    ),
    'autolinks for urls and e-mail addresses' => static fn () => assertSame(
        '<p><a href="https://phpug-dresden.org">https://phpug-dresden.org</a> <a href="mailto:info@phpug-dresden.org">info@phpug-dresden.org</a></p>',
        $md->toHtml('<https://phpug-dresden.org> <info@phpug-dresden.org>'),
    ),
    'hard line break with two trailing spaces' => static fn () => assertSame(
        "<p>Zeile 1<br>\nZeile 2</p>",
        $md->toHtml("Zeile 1  \nZeile 2"),
    ),
    'unordered list with nested ordered list' => static fn () => assertSame(
        '<ul><li>A<ol><li>A1</li><li>A2</li></ol></li><li>B</li></ul>',
        $md->toHtml("- A\n  1. A1\n  2. A2\n- B"),
    ),
    'list item continuation lines' => static fn () => assertSame(
        "<ul><li>Lange\nZeile</li></ul>",
        $md->toHtml("* Lange\n  Zeile"),
    ),
    'raw html blocks pass through until blank line' => static fn () => assertSame(
        "<table>\n<tr><td>**x**</td></tr>\n</table>\n<p>Text</p>",
        $md->toHtml("<table>\n<tr><td>**x**</td></tr>\n</table>\n\nText"),
    ),
    'fenced code is escaped' => static fn () => assertSame(
        '<pre><code class="language-php">echo "&lt;b&gt;";</code></pre>',
        $md->toHtml("```php\necho \"<b>\";\n```"),
    ),
    'blockquote and horizontal rule' => static fn () => assertSame(
        "<blockquote><p>Zitat</p></blockquote>\n<hr>",
        $md->toHtml("> Zitat\n\n---"),
    ),
];
