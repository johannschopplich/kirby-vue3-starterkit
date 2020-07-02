<?= '<?xml version="1.0" encoding="UTF-8"?>'  ?>
<xsl:stylesheet
        version="1.0"
        xmlns:sm="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:mobile="http://www.google.com/schemas/sitemap-mobile/1.0"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:fo="http://www.w3.org/1999/XSL/Format"
        xmlns:xhtml="http://www.w3.org/1999/xhtml"
        xmlns="http://www.w3.org/1999/xhtml">

    <xsl:output method="html" indent="yes" encoding="UTF-8"/>

    <xsl:template match="/">
        <html>
            <head>
                <title>
                    <?= option('cre8ivclick.sitemapper.title') ?>
                    <xsl:if test="sm:sitemapindex"> - Index</xsl:if>
                </title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.2.2/dist/css/uikit.min.css" />
                <style>
<?php
// Possible Custom Colours:
//page background colour
$bgClr = option('cre8ivclick.sitemapper.bgClr');
//normal text colour
$txtClr = option('cre8ivclick.sitemapper.txtClr');
//page title
$titleClr = option('cre8ivclick.sitemapper.titleClr');
//background colour of pill-shaped badges shown next to page title
$badgeBgClr = option('cre8ivclick.sitemapper.badgeBgClr');
//text colour of pill-shaped badges shown next to page title
$badgeTxtClr = option('cre8ivclick.sitemapper.badgeTxtClr');
//colour of divider line below the title, and at the bottom of page
$dividerClr = option('cre8ivclick.sitemapper.dividerClr');
// colour of text in the table column headings
$thClr = option('cre8ivclick.sitemapper.thClr');
// colour of border between table rows
$rowBorderClr = option('cre8ivclick.sitemapper.rowHoverClr') ?: 'lightGray';
// background colour of table rows when hovered:
$rowHoverClr = option('cre8ivclick.sitemapper.rowHoverClr');
//colour of all links on the page
$linkClr = option('cre8ivclick.sitemapper.linkClr');
//colour of links when hovered
$linkHoverClr = option('cre8ivclick.sitemapper.linkHoverClr');
//background colour of disclosure buttons
$btnBgClr = option('cre8ivclick.sitemapper.btnBgClr');
//background colour of disclosure buttons when hovered
$btnBgHoverClr = option('cre8ivclick.sitemapper.btnBgHoverClr');
//colour of disclosure arrow icon inside disclosure buttons
$btnIconClr = option('cre8ivclick.sitemapper.btnIconClr');
//colour of disclosure arrow icon when hovered
$btnIconHoverClr = option('cre8ivclick.sitemapper.btnIconHoverClr');
//colour of icon shown before page/image url
$urlIconClr = option('cre8ivclick.sitemapper.urlIconClr');
//colour of language tag shown after page url
$urlTagClr = option('cre8ivclick.sitemapper.urlTagClr');
// intro text for the start of the page:
$intro = str_replace('[[count]]','<xsl:value-of select="count(sm:urlset/sm:url)"/>',option('cre8ivclick.sitemapper.intro'));
// by-line for the end of the page:
$byLine = option('cre8ivclick.sitemapper.byLine');
?>
                    body {
<?php if($bgClr): ?>
                        background: <?= $bgClr ?>;
<?php endif;
      if($txtClr): ?>
                        color: <?= $txtClr ?>;
<?php endif; ?>
                    }
                    h1.uk-heading-divider {
<?php if($titleClr): ?>
                        color: <?= $titleClr ?>;
<?php endif;
      if($dividerClr):
?>
                        border-bottom-color: <?= $dividerClr ?>;
<?php endif; ?>
                    }
                    h1 .uk-badge {
                        margin-right: 10px;
                        margin-top: 9px;
                        padding: 9px 12px;
<?php if($badgeBgClr): ?>
                        background: <?= $badgeBgClr ?>;
<?php endif;
      if($badgeTxtClr): ?>
                        color: <?= $badgeTxtClr ?>;
<?php endif; ?>
                    }
                    h1 .uk-badge:hover {
<?php if($badgeTxtClr): ?>
                        color: <?= $badgeTxtClr ?>;
<?php else: ?>
                        color: #fff;
<?php endif; ?>
                    }
                    a {
<?php if($linkClr): ?>
                        color: <?= $linkClr ?>;
<?php endif; ?>
                        transition: color 0.4s;
                    }
<?php if($linkHoverClr): ?>
                    a:hover { color: <?= $linkHoverClr ?>; }
<?php endif;
      if($dividerClr):
?>
                    hr { border-color: <?= $dividerClr ?>; }
<?php endif; ?>
                    button.toggle {
                        padding: 0 6px;
                        margin-right: 12px;
                        line-height: 28px;
                        border: none;
<?php if($btnBgClr): ?>
                        background: <?= $btnBgClr ?>;
<?php endif;
      if($btnIconClr): ?>
                        color: <?= $btnIconClr ?>;
<?php endif; ?>
                    }
                    button.toggle:hover {
<?php if($btnBgHoverClr): ?>
                        background: <?= $btnBgHoverClr ?>;
<?php endif;
      if($btnIconHoverClr): ?>
                        color: <?= $btnIconHoverClr ?>;
<?php endif; ?>                    }
                    div.toggle-content { padding: 18px 12px 0 36px;  }
                    span.content-icon {
                        margin-right: 6px;
<?php if($urlIconClr): ?>
                        color: <?= $urlIconClr ?>;
<?php endif; ?>
                    }
                    span.content-tag {
                        margin-left: 9px;
<?php if($urlTagClr): ?>
                        color: <?= $urlTagClr ?>;
<?php endif; ?>
                    }
<?php if($rowBorderClr): ?>
                    tr { border-bottom: 1px solid <?= $rowBorderClr ?>; }
<?php endif;
// if no colour is specified for the column headings, we try to use the body text colour:
      $thClr = option('cre8ivclick.sitemapper.thClr') ?: option('cre8ivclick.sitemapper.txtClr');
      if($thClr): ?>
                    .uk-table th { color: <?= $thClr ?>; }
<?php endif;
      if($rowHoverClr):
?>
                    .uk-table-hover tbody tr:hover { background: <?= $rowHoverClr ?>; }
<?php endif; ?>
                </style>
                <script src="https://cdn.jsdelivr.net/npm/uikit@3.2.2/dist/js/uikit.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/uikit@3.2.2/dist/js/uikit-icons.min.js"></script>
            </head>
            <body>
                <div class="uk-container">
                <h1 class="uk-heading-divider uk-margin-large-top">
                    <?= option('cre8ivclick.sitemapper.title') ?>
                    <xsl:if test="sm:sitemapindex">Index</xsl:if>
                    <xsl:if test="sm:urlset/sm:url/mobile:mobile">
                        <span  class="uk-badge">mobile</span>
                    </xsl:if>
                    <xsl:if test="sm:urlset/sm:url/image:image">
                        <span  class="uk-badge"><?= option('cre8ivclick.sitemapper.imagesStr') ?></span>
                    </xsl:if>
                    <xsl:if test="sm:urlset/sm:url/news:news">
                        <span  class="uk-badge">news</span>
                    </xsl:if>
                    <xsl:if test="sm:urlset/sm:url/video:video">
                        <span  class="uk-badge">videos</span>
                    </xsl:if>
                    <xsl:if test="sm:urlset/sm:url/xhtml:link">
                        <span  class="uk-badge"><?= option('cre8ivclick.sitemapper.alternatesStr') ?></span>
                    </xsl:if>
                </h1>
                <p>
                <xsl:choose>
                    <xsl:when test="sm:sitemapindex">
                        This sitemap index file contains
                        <strong><xsl:value-of select="count(sm:sitemapindex/sm:sitemap)"/></strong>
                        sitemaps.
                    </xsl:when>
                    <xsl:otherwise>
                        <?= $intro ?>
                    </xsl:otherwise>
                </xsl:choose>
                </p>

                <xsl:apply-templates/>
                <p class="uk-text-small uk-text-center">
                    <?= $byLine ?>

                </p>
                <hr class="uk-margin-large-bottom" />
            </div>
            <script type="text/javascript">
                var elements = document.querySelectorAll('button.toggle');
                var pos;
                elements.forEach(function(el){
                    el.addEventListener('beforeshow', function(e){
                        pos = e.target.scrollTop;
                        console.log('beforeshow processed');
                    });
                    el.addEventListener('shown', function(e){
                        e.target.scrollTop = pos;
                    });
                    el.addEventListener('beforehide', function(e){
                        pos = e.target.scrollTop;
                    });
                    el.addEventListener('hidden', function(e){
                        e.target.scrollTop = pos;
                    });
                });
            </script>
            </body>
        </html>
    </xsl:template>


    <xsl:template match="sm:sitemapindex">
        <table class="uk-table uk-table-hover">
            <tr>
                <th></th>
                <th><?= option('cre8ivclick.sitemapper.urlStr') ?></th>
                <th><?= option('cre8ivclick.sitemapper.lastModStr') ?></th>
            </tr>
            <xsl:for-each select="sm:sitemap">
                <tr>
                    <xsl:variable name="loc">
                        <xsl:value-of select="sm:loc"/>
                    </xsl:variable>
                    <xsl:variable name="pno">
                        <xsl:value-of select="position()"/>
                    </xsl:variable>
                    <td>
                        <xsl:value-of select="$pno"/>
                    </td>
                    <td>
                        <a href="{$loc}">
                            <xsl:value-of select="sm:loc"/>
                        </a>
                    </td>
                    <xsl:apply-templates/>
                </tr>
            </xsl:for-each>
        </table>
    </xsl:template>

    <xsl:template match="sm:urlset">
        <table class="uk-table uk-table-hover uk-table-small">
            <tr>
                <th></th>
                <th><?= option('cre8ivclick.sitemapper.urlStr') ?></th>
                <xsl:if test="sm:url/sm:lastmod">
                    <th><?= option('cre8ivclick.sitemapper.lastModStr') ?></th>
                </xsl:if>
                <xsl:if test="sm:url/sm:changefreq">
                    <th>Change Frequency</th>
                </xsl:if>
                <xsl:if test="sm:url/sm:priority">
                    <th>Priority</th>
                </xsl:if>
            </tr>
            <xsl:for-each select="sm:url">
                <tr>
                    <xsl:variable name="loc">
                        <xsl:value-of select="sm:loc"/>
                    </xsl:variable>
                    <xsl:variable name="pno">
                        <xsl:value-of select="position()"/>
                    </xsl:variable>
                    <td>
                        <xsl:value-of select="$pno"/>
                    </td>
                    <td>
                        <button type="button" class="uk-button uk-button-default toggle" uk-toggle="target: .toggle-{$pno}; animation: uk-animation-slide-top-small">
                        <span class="toggle-{$pno}" uk-icon="icon: triangle-down"></span>
                        <span class="toggle-{$pno}" uk-icon="icon: triangle-up" hidden="true"></span>
                        </button>
                        <a href="{$loc}">
                            <xsl:value-of select="sm:loc"/>
                        </a>
                        <div class="toggle-{$pno} toggle-content"  hidden="true">
                            <xsl:apply-templates select="xhtml:*"/>
                            <xsl:apply-templates select="image:*"/>
                            <xsl:apply-templates select="video:*"/>
                        </div>
                    </td>
                    <xsl:apply-templates select="sm:*"/>
                </tr>
            </xsl:for-each>
        </table>
    </xsl:template>

    <xsl:template match="sm:loc|image:loc|image:caption|video:*">
    </xsl:template>

    <xsl:template match="sm:lastmod|sm:changefreq|sm:priority">
        <td>
            <xsl:apply-templates/>
        </td>
    </xsl:template>

    <xsl:template match="xhtml:link">
        <xsl:variable name="altloc">
            <xsl:value-of select="@href"/>
        </xsl:variable>
        <p>
            <span class="content-icon" uk-icon="icon: file-text"></span>
            <a href="{$altloc}">
                <xsl:value-of select="@href"/>
            </a>
            <span class="uk-text-meta content-tag">
                <xsl:value-of select="@hreflang"/>
            </span>
        </p>
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="image:image">
        <xsl:variable name="loc">
            <xsl:value-of select="image:loc"/>
        </xsl:variable>
        <p>
            <span class="content-icon" uk-icon="icon: image"></span>
            <a href="{$loc}">
                <xsl:value-of select="image:loc"/>
            </a>
            <span>
                <xsl:value-of select="image:caption"/>
            </span>
            <xsl:apply-templates/>
        </p>
    </xsl:template>
    <xsl:template match="video:video">
        <xsl:variable name="loc">
            <xsl:choose>
                <xsl:when test="video:player_loc != ''">
                    <xsl:value-of select="video:player_loc"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="video:content_loc"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <p>
            Video:
            <a href="{$loc}">
                <xsl:choose>
                    <xsl:when test="video:player_loc != ''">
                        <xsl:value-of select="video:player_loc"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="video:content_loc"/>
                    </xsl:otherwise>
                </xsl:choose>
            </a>
            <span>
                <xsl:value-of select="video:title"/>
            </span>
            <span>
                <xsl:value-of select="video:thumbnail_loc"/>
            </span>
            <xsl:apply-templates/>
        </p>
    </xsl:template>
</xsl:stylesheet>
