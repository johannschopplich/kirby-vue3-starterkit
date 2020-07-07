<?php

use KirbyExtended\MetaTags;
use PHPUnit\Framework\TestCase;

class MetaTagsTest extends TestCase
{
    /**
     * Tags list
     *
     * @var object
     */
    protected object $head;

    public function setUp(): void
    {
        $this->head = new MetaTags();
    }

    public function testTitleTag()
    {
        $tag = $this->head->title('"Title tag" test');

        $this->assertEquals('<title>&quot;Title tag&quot; test</title>', $tag);
    }

    public function testArrayOfTags()
    {
        $tag1 = $this->head->link('alternate', [
            'hreflang' => 'pt-br',
            'href' => 'https://br.pedroborg.es'
        ]);

        $tag2 = $this->head->link('alternate', [
            'hreflang' => 'en-us',
            'href' => 'https://en.pedroborg.es'
        ]);

        $html = $this->head->render();

        $expectedHtml = <<<'EOD'
<link rel="alternate" hreflang="pt-br" href="https://br.pedroborg.es">
    <link rel="alternate" hreflang="en-us" href="https://en.pedroborg.es">

EOD;

        $this->assertEquals($expectedHtml, $html);
    }

    public function testLinkTag()
    {
        $tag       = $this->head->link('canonical', 'https://pedroborg.es');
        $alternate = $this->head->link('alternate', [
            'hreflang' => 'pt-br',
            'href' => 'https://br.pedroborg.es'
        ]);
        $empty     = $this->head->link('dns-prefetch', '');

        $this->assertEquals(
            '<link rel="canonical" href="https://pedroborg.es">',
            $tag
        );

        $this->assertEquals(
            '<link rel="alternate" hreflang="pt-br" href="https://br.pedroborg.es">',
            $alternate
        );

        $this->assertEquals('', $empty);
    }

    public function testMetaTag()
    {
        $tag     = $this->head->meta('description', 'Meta tag test');
        $encoded = $this->head->meta('description', '"Meta tag" test');
        $empty   = $this->head->meta('description', '');

        $this->assertEquals(
            '<meta name="description" content="Meta tag test">',
            $tag
        );

        $this->assertEquals(
            '<meta name="description" content="&quot;Meta tag&quot; test">',
            $encoded
        );

        $this->assertEquals('', $empty);
    }

    public function testOpenGraphTag()
    {
        $tag       = $this->head->og('title', 'Open Graph test');
        $preffixed = $this->head->og('og:title', 'Open Graph test', false);
        $empty     = $this->head->og('og:title', '', false);

        $this->assertEquals(
            '<meta property="og:title" content="Open Graph test">',
            $tag
        );

        $this->assertEquals(
            '<meta property="og:title" content="Open Graph test">',
            $preffixed
        );

        $this->assertEquals('', $empty);
    }

    public function testTwitterCardTag()
    {
        $tag       = $this->head->twitter('card', 'summary');
        $preffixed = $this->head->twitter('twitter:card', 'summary', false);
        $empty     = $this->head->twitter('twitter:image', '', false);

        $this->assertEquals('<meta name="twitter:card" content="summary">', $tag);
        $this->assertEquals('<meta name="twitter:card" content="summary">', $preffixed);
        $this->assertEquals('', $empty);
    }

    public function testLinkedDataTag()
    {
        $tag = $this->head->jsonld([
            '@context' => 'http://schema.org/',
            '@type' => 'MusicAlbum',
            'name' => 'Music album test'
        ]);

        $expectedHtml = <<<'EOD'
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "MusicAlbum",
    "name": "Music album test"
}
</script>
EOD;

        $this->assertEquals($expectedHtml, $tag);
    }

    public function testRendering()
    {
        $this->head->link('canonical', 'https://pedroborg.es');
        $this->head->twitter('card', 'summary');
        $this->head->title('<title> tag test');
        $this->head->og('title', 'Open Graph test');
        $this->head->meta('description', '"Meta Tags" test');
        $this->head->jsonld([
            '@context' => 'http://schema.org',
            '@type' => 'Organization',
            'name' => 'Example Co',
            'url' => 'https://www.example.com'
        ]);

        $html = $this->head->render();

        $expectedHtml = <<<'EOD'
<title>&lt;title&gt; tag test</title>
    <meta name="description" content="&quot;Meta Tags&quot; test">
    <meta property="og:title" content="Open Graph test">
    <meta name="twitter:card" content="summary">
    <link rel="canonical" href="https://pedroborg.es">
    <script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "Organization",
        "name": "Example Co",
        "url": "https://www.example.com"
    }
    </script>

EOD;

        $this->assertEquals($expectedHtml, $html);
    }

    public function testRenderingGroup()
    {
        $this->head->link('canonical', 'https://pedroborg.es');
        $this->head->twitter('card', 'summary');

        $this->assertEquals(
            '<meta name="twitter:card" content="summary">',
            trim($this->head->render('twitter'))
        );
    }

    public function testRenderingGroups()
    {
        $this->head->link('canonical', 'https://pedroborg.es');
        $this->head->twitter('card', 'summary');
        $this->head->title('<title> tag test');
        $this->head->og('title', 'Open Graph test');
        $this->head->meta('description', '"Meta Tags" test');

        $html = $this->head->render(['og', 'meta']);

        $expectedHtml = <<<'EOD'
<meta property="og:title" content="Open Graph test">
    <meta name="description" content="&quot;Meta Tags&quot; test">

EOD;

        $this->assertEquals($expectedHtml, $html);
    }
}
