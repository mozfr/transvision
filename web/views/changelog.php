<h2 id="v3.0">Version 2.10 - 2013-12-??</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New view:</strong> <a href="/3locales/">3 locales search</a> in your strings for potential errors (pascal &amp; filip)</li>
    <li><strong>New view:</strong> Check the <a href="http://transvision-beta.mozfr.org/variables/">use of variables</a> in your strings for potential errors (pascal)</li>
    <li>On the <a href="/gaia/#englishchanges">Gaia comparizon</a> view list the strings that have changed significantly in English between Gaia 1.1 and 1.2 releases without an entity change (pascal)</li>
    <li>When searching for entities, there is a 'search a bug' link below results just like for searching for strings (flod)</li>
    <li>Each view now has a one line description of what it does at the top (Jesus)</li>
    <li>Updated supported locales (flod)</li>
    <li>Added Gaia 1.3 support (flod)</li>
    <li>The <a href="http://transvision-beta.mozfr.org/">beta site</a> now has a Beta ribbon to distinguish it from the main site (flod)</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Page load performance with the use of cached reduced json data from Bugzilla (flod)</li>
</ul>

<h3>Developer visible changes</h3>
<ul>
    <li>Added Kint debugging library in development mode (pascal)</li>
    <li>Coding guidelines roughly formalized as following PSR recommandations and <a href="https://github.com/mozfr/transvision/wiki/Code-conventions">documented on wiki</a> (pascal)</li>
    <li>Various code cleanups (pascal)</li>
<ul>

<h2 id="v2.9">Version 2.9 - 2013-10-26</h2>

<h3>End user visible changes</h3>
<ul>
    <li>Two new repos are added for searches: Gaia 1.1 and Gaia 1.2 (Pascal)</li>
    <li>New (experimental) <a href="/gaia/">QA view for strings in Gaia repos</a> listing the differences in translations for the same entities across gaia-l10n, gaia_1.1 and gaia_1.2 repos as well as listing all the strings added to Gaia 1.2 as they require more attention (Pascal)</li>
    <li>More accurate comparaison of string length in the main view ('large string' warning no longer has false positives)</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Data is now updated from hg updated 4 times a day at 04:00, 10:00, 16:00, 22:00 (was twice a day)</li>
    <li>Data extraction script is now interruptible to make development easier (flod)</li>
    <li>Cron job issues fixed after our migration to Debian 7 (pascal)</li>
    <li><a href="irc://irc.mozilla.org/transvision">#transvision</a> IRC channel created and added to our documentation (pascal and chandankumar)</li>
    <li>Fix broken Statistics link (Jesũs)</li>

</ul>

<h2 id="v2.8">Version 2.8 - 2013-08-09</h2>

<h3>End user visible changes</h3>
<ul>
    <li>Make search context changes clearer, the type of search (strings, entities, strings &amp; entities) is now displayed as a hint below the search field (Francesco) </li>
    <li>Add anchors to search results so as to be able to give a link pointing to a specific entity in a search result page (<a href="/?repo=central&sourcelocale=en-US&locale=en-US&search_type=strings&recherche=bookmarks#browser_chrome_browser_aboutPrivateBrowsing.dtd_privatebrowsingpage.perwindow.description">example</a> (Francesco)</li>
    <li>Fix bugzilla links to file a bug in the right Bugzila component for locales that have a specific locale code in Gaia that differs from Desktop (es vs es-ES, sr vs sr-Cyrl/Latn&hellip;) (Pascal)</li>
</ul>

<h3>Developer visible changes</h3>
<ul>
    <li>The Json API no longer does any locale detection or fallbacks to French if the locale does not exist for the repo (Pascal)</li>
    <li><strong>Experimental</strong> The searchrepo view can also be <a href="/showrepos/?json">output as json</a> for external dashboards (Pascal).</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Server changes to make Transvision compatible with l20n sources (pascal)</li>
    <li>Server changes to pull from <var>hg default</var> instead of <var>hg tip</var> for automatic updates, fixes various result pages problems (flod)</li>
</ul>

<h2 id="v2.7">Version 2.7 - 2013-07-05</h2>

<h3>End user visible changes</h3>
<ul>
    <li>The repository select box is now located on the far left of the search page. Switching repositories now dynamically updates the select boxes for locales with the right locales (Jesús)</li>
    <li>The TMX download page lists downloads per channel more accurately and takes Gaia specific language code (<var>es</var> for common spanish for example) into account (Jesús)</li>
    <li>Gaia locales update: (Pascal)
        <ul>
            <li>Added ast, bg, bn-IN, da, gu, hr, km, ne-NP, pa, si, sk, sr-Cyrl, th, ur, vi.</li>
            <li>Removed as, ga-IE, gl, ml, or.</li>
        </ul>
    </li>
    <li>Desktop locales update: (Pascal)
        <ul>
            <li>Central: removed ach, my, wo.</li>
            <li>Release: removed an, my, wo.</li>
        </ul>
    </li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>New <a href="/showrepos">experimental view</a> to see the state of all locales per repo (Pascal)</li>
    <li>CSS and HTML cleanups of views (flodolo)</li>
    <li>Links to source code sometimes were linking to the wrong repository (Pascal)</li>
    <li>Various bugs fixes related to string extractions and mercurial repositories updates (Pascal)</li>
</ul>

</ul>
<h2 id="v2.6">Version 2.6 - 2013-06-14</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> All TMX files for all locales/repos combinations are now available via a <a href="http://transvision.mozfr.org/downloads/">TMX download page</a> (Jesús, Pascal)</li>
    <li><quote>Translate with:</quote> links in search results are now below the source English strings to point to Bing and Google translation services (Jesús)</li>
    <li>In the search results page for string searches, the entity names in the first columns are now links to the entities for easy sharing/bookmarking (Pascal)</li>
    <li>CSS fixes(Jesús)</li>
</ul>

<h2 id="v2.5">Version 2.5 - 2013-04-18</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> Updated visual theme inspired by Mozilla Sandstone, responsive design (Jesús and Pascal).</li>
    <li>We no longer highlight string matches in the entities column for the 'string' search type (Jesús).</li>
</ul>

<h3>Developer visible changes</h3>
<ul>
    <li>There is now a Json/JsonP source for searches filtered on entities, the results structure is the same as searches on strings content (Pascal). Append <code>&amp;json</code> to your query for the Json feed and <code>&amp;json&amp;callback=foobar</code> for JsonP.</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Updated setup.sh script to fix bugs in the installer and make it easier for potential contributors to install the application (Pascal).</li>
    <li>The <a href="http://babelwiki.babelzilla.org/index.php?title=MozTran">MozTran Firefox extension</a> was updated and allow now to search for an entity you select on a page from the context menu. This is mostly useful when looking at the product Dashboard (Goofy and Pascal).</li>
    <li>Update to track the recent <var>webapprt</var> folder move.</li>
</ul>
<h2 id="v2.4">Version 2.4 - 2013-04-10</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> There is now a &lt;report a bug&gt; link below each translated string that allows anybody to report a bug in Bugzilla for a badly translated string<em>(Jesús and Pascal)</em></li>
</ul>

<h2 id="v2.3">Version 2.3 - 2013-03-22</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> You now can remember your locales and repo choices and bypass locale detection with checkboxes, a cookie will be set and remember your preferences <em>(Jesús)</em></li>
    <li><strong>New feature:</strong> If your string is abnormally long or short compared to the English source, there will be hint message below your translation saying 'String too large/long?' <em>(Jesús)</em></li>
    <li>The accesskeys and channel comparizon features linked in the footer now work again <em>(pascal)</em></li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Gaia strings are now listed for Spanishes for searches done on Desktop repos (<em>pascal</em>)</li>
    <li>Removed Firefox Mobile Xul code and data (<em>pascal</em>)</li>
    <li>Bug fixes, code cleanups.</li>
</ul>

<h2 id="v2.2">Version 2.2 - 2013-02-28</h2>

<h3>End user visible changes</h3>
<ul>
    <li>Selected search type value (strings, entities, strings and entities) is kept after a search.</li>
    <li>Searches with slashes in the middle of strings now work</li>
    <li>Fixed a bug in the cron job script that resulted in mozilla-central strings to not be extracted</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Cron job is now ran twice a day instead of once, at 2AM CET and 2PM CET</li>
    <li>Stats page now lists type of string searches and repos. All stats reset to zero.</li>
</ul>

<h2 id="v2.1">Version 2.1 - 2013-01-30</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> New select box option to search in strings, entities, or both strings and entities.</li>
    <li><strong>New feature:</strong> typography hint, if a sentence ends with a dot and your translation doesn't, there is a small "No final dot?" warning below the string.</li>
    <li>Missing translated strings are marked as such instead of just having an empty cell.</li>
    <li>Repositories and search targets are now select boxes.</li>
    <li>Advanced regular expression search options are now separated from other search options.</li>
    <li>Glossary search is now a separate view linked in the footer.</li>
    <li>Improved highlighting of search results.</li>
    <li>Fixed some bugs on "perfect match" searches.</li>
    <li>Search results now limited to 200 results to avoid hanging the browser if we send back megabytesof data.</li>
    <li>Added Aragonese</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Added a <a href="/credits/">credits</a> page.</li>
    <li>Added a counter for the use of search options to check if some options are unused and could be removed.</li>
</ul>

<h2 id="v2.0">Version 2.0 - 2013-01-18</h2>

<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> In search results, there is now a <em>source</em> link next to the original string and your translation, this links to the file on hg.mozilla.org, this way you can find easily where the file to edit is.</li>
    <li>The entity names in the first column now longer link to a search for the English file on mxr now that we have proper link support to the source file.</li>
    <li>Searches containing a slash (/) now have results.</li>
    <li>Manifest.properties files containing name and description od apps in the Gaia repo are now shown in search results.</li>
    <li>A regression on the Glossary search is fixed, it is now again yielding results.</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Transvision jumps from version 1.9 to version 2.0 (and not 1.10), this is partly because the addition of source strings was significant development work which involved data structure changes and partly because I don't want to end up with a version 1.230 at some point ;).</li>
    <li>The <a href="/stats/">statistics page</a> view added last week now sorts locales per number of requests and gives totals of searches and locales.</li>
</ul>

<h2 id="v1.9">Version 1.9 - 2013-01-11</h2>

<h3>End user visible changes</h3>
<ul>
    <li>Searches including  special characters such as [, (, { are now possible (useful for plurals in Gaia entities, replacement variables in Gais strings or output of messages containing function names in developer tools) </li>
    <li>Occitan locale added</li>

</ul>

<h3>Code changes</h3>
<ul>
    <li>PHP: Monolog library updated to 1.3.0</li>
    <li>Python: Silme library updated to 0.8.1</li>
    <li>fixed an error in Setup shell script</li>
    <li>Move classes to a Transvision namespace</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>New short <a href="/stats/">statistics page</a> showing which locales use Transvision</li>
</ul>

<h2 id="v1.8">Version 1.8 - 2013-01-04</h2>

<h3>End user visible changes</h3>
<ul>
    <li>Searches need to be at least 2 characters long, single letter searches now return an error message.</li>
    <li>Style improvements to search forms and the template for better clarity.</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>External dependencies are now all managed through <a href="http://getcomposer.org/">Composer</a></li>
    <li>Added Monolog library to be able to log events related to debugging or the use of the application (/ex: which locales actually do use Transvision)</li>
</ul>

<h2 id="v1.7">Version 1.7 - 2012-10-24</h2>
<h3>End user visible changes</h3>
<ul>
    <li><strong>New experimental feature:</strong> You can now see all the translated <a href="/accesskeys/">access keys</a> that are potentially wrong for your locale. If you see a redish square next to your access key letter such as this one&nbsp;: <span class="highlight-red">&nbsp;</span>, it means that there is a space in your string and the access key may not work.</li>
    <li>Access keys are now part of search results</li>
    <li>Experimental views are now linked in the footer of the site</li>
</ul>
<h3>Developer visible changes</h3>
<ul>
    <li>Fixed a regression on the JSON api results (double quotes no longer escaped and breaking JSON format).</li>
    <li>Added back proper JSONP support activated with the <var>callback</var> GET parameter, sent with the  application/javascript Mime type.</li>
</ul>
<h2 id="v1.6">Version 1.6 - 2012-10-18</h2>
<h3>End user visible changes</h3>
<ul>
    <li><strong>New experimental feature:</strong> You can now compare differences in your translations across channels (central, aurora, beta, release) on this page <a href="/channelcomparison/">Channel to Channel differences</a></li>
    <li>Thunderbird's Chat strings are now included.</li>
    <li>Mozilla Central is now the default repo for searches (instead of Release)</li>
    <li>Removed regular expression searches to unclutter the search panel (Wildcard and case sensitive are still there)</li>
    <li>Fixed a bug in causing the loss of the source locale for Spanish when switching from a search on Gaia to a search on comm/moz-central repos</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Lots of refactoring, the code is now stable enough to experiment with new views such as the Channel to Channel comparison page, with little to no impact on the main search feature for the application. That should allow specific views per locale and experiments.</li>
</ul>

<h2 id="v1.5">Version 1.5 - 2012-10-02</h2>
<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> Gaia strings are now included and merged with your repos. You can also do searches for the Gaia repo only</li>
    <li>Results are more accurate, specifically, identical strings between gecko apps are always shown.</li>
    <li>Ellipsis are shown with a gray background, thin spaces with a red background, thin spaces and non-breakable spaces have a tooltip to distinguish them (those changes are mostly helpers for French typography rules).</li>
</ul>
<h3>Developer visible changes</h3>
<ul>
    <li>The json api now returns <code>[]</code> instead of <code>null</code> if the search yields no result.</li>
</ul>
<h3>Other changes</h3>
<ul>
    <li>There is now a Transvision Beta server at <a href="http://transvision-beta.mozfr.org">transvision-beta.mozfr.org</a>, if you find a bug or a regression on Transvision, please check on this beta server that the bug you want to report is not already fixed.</li>
    <li>Set up a basic url front controller to be able to use the new PHP 5.4 integrated web server for development and also installed the Atoum Unit Test framework.</li>
    <li>Now with the MozFR favicon :)</li>
</ul>
<h2 id="v1.4">Version 1.4 - 2012-09-04</h2>
<h3>End user visible changes</h3>
<ul>
    <li><strong>New feature:</strong> locale to locale comparison. There is now two locale switchers, the source and the target one. By default, the source is en-US and the target is your detected locale code. You can manually set a different source than en-US so as to compare your translations with another locale. Note that the search results will be limited to the amount of translated strings in the source locale.</li>
    <li>Strings in MXR searches are now truncated if they exceed MXR's field length limits, it prevents an MXR error message and usually gives good search results</li>
    <li>The second table of results was showing the translation in both columns, this regression is fixed.</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>The cron job updating the repositories on MozFR server was not behaving correctly and repos were not updated in the last week, this is now fixed</li>
</ul>

<h2 id="v1.3">Version 1.3 - 2012-08-17</h2>
<h3>End user visible changes</h3>
<ul>
    <li>Strings in .ini and .inc files are now also in results</li>
    <li>Non-breakable spaces are shown with a gray background in search results, this is useful for languages like French that have punctuation rules stating that some puntuation signs (?!;«») should stick to the previous word but with a spacing.</li>
    <li>Entity search was not searching into all available entities (only about 60% of them), fixed</li>
    <li>Experimental: link English strings to a google translate search</li>
    <li>Visual update of search results</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>The suite/debugQA strings in English are no longer extracted  because they are not meant to be translated (<a href="https://bugzilla.mozilla.org/show_bug.cgi?id=782243">bug 782243</a>)</li>
</ul>

<h2 id="v1.2">Version 1.2 - 2012-08-10</h2>
<h3>End user visible changes</h3>
<ul>
    <li>Searches for strings with single and double quotes work ex: <a href="/?locale=fr&amp;repo=release&amp;recherche=Don't">Search for « Don't »</a></li>
    <li>The <em>Glossary</em> option now yields results that make sense, when you select it all other checkboxes are unselected, ex: <a href="/?locale=fr&amp;repo=release&amp;t2t=t2t&amp;recherche=bookmarks">Search for « Bookmarks »</a>. <br/>That option looks for the closest matches for your locale for a word or a few workd and lists them all. It also lists examples of use. The main use for that is to quickly check how a word is usually translated by your team.</li>
    <li><em>Perfect Match</em> option now actually works</li>
    <li>Changelog page uses the same template as the application</li>
</ul>

<h3>Developer visible changes</h3>
<ul>
    <li>json webservice is now available from a normal search if you add <code>&amp;json</code> to your search query (webservice.php is still available so as to not break API consumers, please udate your script to use <code>index.php?json</code> instead. ex: <a href="/?locale=fr&amp;repo=release&amp;recherche=Don't&amp;json">Search for « Don't »</a></li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Simplification of the python script creating TMX files</li>
    <li>Overall simplification of the PHP code to remove dead code</li>
</ul>

<h2 id="v1.1">Version 1.1 - 2012-08-01</h2>
<h3>End user visible changes</h3>
<ul>
    <li>added ach, ff, lij, my, wo locales</li>
    <li>removed oc, mn locales</li>
    <li>results for rtl locales are now correctly aligned</li>
    <li>added locale detection to populate the default locale on home page</li>
    <li>Radio buttons are now clickable</li>
    <li>cleaned up template to hopefully look better and be more readable</li>
</ul>

<h3>Developer visible changes</h3>
<ul>
    <li>json webservice now sends results with application/json Mime type instead of text/html</li>
    <li>install script setup.sh decoupled from glossaire.sh which updates an existing installation</li>
</ul>

<h3>Other changes</h3>
<ul>
    <li>Lots of code clean ups and simplifications</li>
</ul>

<h2 id="v1.0">Version 1.0 - 2012-07-27</h2>
<ul>
    <li>Initial import of existing code into github and reinstalling on MozFR server</li>
    <li>New URL is <a href="http://transvision.mozfr.org">http://transvision.mozfr.org</a></li>
</ul>
