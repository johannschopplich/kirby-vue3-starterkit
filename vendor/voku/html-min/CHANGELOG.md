# Changelog 4.4.8 (2020-08-11) 

- remove content before "<!doctype.*>", otherwise DOMDocument cannot handle the input
- use a new version of "voku/simple_html_dom" (4.7.22)

# Changelog 4.4.7 (2020-08-11) 

- use a new version of "voku/simple_html_dom" (4.7.21)

# Changelog 4.4.6 (2020-08-08)

-  fix invalid input html

# Changelog 4.4.5 (2020-08-06)

- allow to configure special comments via "setSpecialHtmlComments()"

# Changelog 4.4.4 (2020-08-06)

- fix problems with self-closing-tags e.g. <wbr>

# Changelog 4.4.3 (2020-04-06)

- fix "domNodeClosingTagOptional()" -> fix logic of detecting next sibling dom node

# Changelog 4.4.2 (2020-04-06)

- fix "domNodeClosingTagOptional()" -> do not remove "</p>" if there is more content in the parent node

# Changelog 4.4.1 (2020-04-05) 

- use a new version of "voku/simple_html_dom" (4.7.16)

# Changelog 4.4.0 (2020-04-05)

- add support for removing more default attributes
- add "doRemoveDefaultTypeFromButton()"
- add "doRemoveDefaultMediaTypeFromStyleAndLinkTag()"
- add "doRemoveDeprecatedTypeFromStyleAndLinkTag()"
- add "overwriteTemplateLogicSyntaxInSpecialScriptTags()"
- use a new version of "voku/simple_html_dom" (4.7.15)

# Changelog 4.3.0 (2020-03-22)

- add "isHTML4()"
- add "isXHTML()"
- fix "remove deprecated script-mime-types"
- use a new version of "voku/simple_html_dom" (4.7.13)


# Changelog 4.2.0 (2020-03-06)

- add "doKeepHttpAndHttpsPrefixOnExternalAttributes(bool)": keep http:// and https:// prefix for external links | thanks @abuyoyo
- add "doMakeSameDomainsLinksRelative(string[] $localDomains)": make the local domains relative | thanks @abuyoyo
- optimized "optgroup"-html compressing
- use a new version of "voku/simple_html_dom" (4.7.12)


# Changelog 4.1.0 (2020-02-06)

- add "doRemoveHttpsPrefixFromAttributes()": remove optional "https:"-prefix from attributes (depends on "doOptimizeAttributes(true)")


# Changelog 4.0.7 (2019-11-18)

- fix: too many single white spaces are removed


# Changelog 4.0.6 (2019-10-27)

- fix: fix regex for self-closing tags
- optimize performance via "strpos" before regex


# Changelog 4.0.5 (2019-09-19)

- fix: protect "nocompress"-tags before notifying the Observer


# Changelog 4.0.4 (2019-09-17)

- fix: removing of dom elements


# Changelog 4.0.3

- fix: removing of "\</p>"-tags


# Changelog 4.0.2

- use new version of "voku/simple_html_dom"


# Changelog 4.0.1

- optimize unicode support
- fix: remove unnecessary \</source> closing tag #40
- fix: bad minify text/x-custom-template #38


# Changelog 4.0.0

- use interfaces in the "HtmlMinDom"-Observer

-> this is a BC, but you can simply replace this classes in your observer implementation:

---> "SimpleHtmlDom" with "SimpleHtmlDomInterface

---> "HtmlMin" with "HtmlMinInterface"   


# Changelog 3.1.8

- fix / optimize: "doRemoveOmittedQuotes" -> support for "\<html ⚡>" via SimpleHtmlDom


# Changelog 3.1.7

- fix: "'" && '"' in attributes


# Changelog 3.1.6

- fix: keep HTML closing tags in \<script> tags 


# Changelog 3.1.5

- fix: keep newlines in e.g. "pre"-tags
- fix: remove newlines from "srcset" and "sizes" attribute


# Changelog 3.1.4 (2019-02-28)

- fix: get parent node
- code-style: remove "true" && "false" if return type is bool


# Changelog 3.1.1 / 3.1.2 / 3.1.3 (2018-12-28)

- use new version of "voku/simple_html_dom"


# Changelog 3.1.0 (2018-12-27)

- add "HtmlMinDomObserverInterface" (+ HtmlMin as Observable)
- use phpcs fixer


# Changelog 3.0.6 (2018-12-01)

- implement the "\<nocompress>"-tag + tests


# Changelog 3.0.5 (2018-10-17)

- update vendor (voku/simple_html_dom >= v4.1.7) + fix entities (&lt;, &gt;)


# Changelog 3.0.4 (2018-10-07)

- update vendor (voku/simple_html_dom >= v4.1.6) + option for keep broken html


# Changelog 3.0.3 (2018-05-08)

- update vendor (voku/simple_html_dom >= v4.1.4)


# Changelog 3.0.2 (2018-02-12)

- fix regex for self-closing tags


# Changelog 3.0.1 (2017-12-29)

- update vendor (voku/simple_html_dom >= v4.1.3)


# Changelog 3.0.0 (2017-12-22)

- remove "Portable UTF-8" as required dependency

-> this is a breaking change, without any API changes


# Changelog 2.0.4 (2017-12-22)

- check if there was already whitespace e.g. from the content


# Changelog 2.0.3 (2017-12-22)

- fix "Minifier removes spaces between tags"
- fix "Multiple horizontal whitespace characters not collapsed"


# Changelog 2.0.2 (2017-12-10)

- try to fix "Minifier removes spaces between tags" v2
- disable "doRemoveWhitespaceAroundTags" by default


# Changelog 2.0.1 (2017-12-10)

- try to fix "Minifier removes spaces between tags" v1


# Changelog 2.0.0 (2017-12-03)

- drop support for PHP < 7.0
- use "strict_types"
- doRemoveOmittedQuotes() -> remove quotes e.g. class="lall" => class=lall
- doRemoveOmittedHtmlTags() -> remove ommitted html tags e.g. \<p>lall\</p> => \<p>lall 
