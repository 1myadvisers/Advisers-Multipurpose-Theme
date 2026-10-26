=== Advisers Multipurpose ===
Contributors: (add your WordPress.org username here before submitting)
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A multipurpose, SEO-friendly, fast-loading theme with a custom header, footer, sidebar, and a native block-pattern page builder — fully customizable typography, colors, blog layout, and social icons, with a light/dark color scheme.

== Description ==

Advisers Multipurpose is a multipurpose WordPress theme built with vanilla CSS and JavaScript only — no bundled framework, no jQuery dependency — for fast page loads and a small footprint. It includes a custom header, footer, and sidebar (all controllable from the Customizer), plus native block patterns so pages can be assembled from ready-made sections using only WordPress core's block editor, with no third-party page-builder plugin required.

**Page templates**

* Default Template (page.php) — used automatically for every new page. Includes header, footer, and an optional sidebar (toggle it on/off per page); controlled from Appearance > Customize > Advisers Multipurpose — Layout Options, with a per-page override in the "Layout Options" box on the page editor screen. Block patterns can be inserted directly into this template's content.
* Landing Page (No Header/Footer) — a bare canvas for standalone landing pages, built entirely from block patterns.

**Page builder (block patterns)**

Open the block inserter on any page and look for the "Advisers Multipurpose — Page Sections" category: Hero Section, Features Grid, Call To Action Band, Testimonials, and Pricing Table. All patterns are built from core WordPress blocks only.

**Heading structure**

Pages (post type "page") show their title as an H2 in the entry header, not H1, so a title is always visible without risking a duplicate H1 if a page also uses the "Hero Section" pattern (which includes its own H1). Blog posts, archives, search results, and the 404 page each get exactly one theme-printed H1 automatically.

**Customizer options**

Appearance > Customize > Advisers Multipurpose — Layout Options includes:

* Global Layout — container width, default sidebar position, breadcrumbs toggle
* Header Settings — CTA button text/URL, Header Background Color, Navigation Bar Background Color
* Footer Settings — widget column count, copyright text
* Brand Colors — primary color, secondary/text color, and a Color Scheme option (Light / Auto — matches each visitor's device / Dark). Header and navigation bar colors always keep whatever you set above, regardless of scheme; only body content, cards, and borders switch.
* Typography — font style (four zero-external-request system font stacks: System Default, Modern Sans, Classic Serif Headings, Rounded), base text size, and heading size scale
* Blog & Archive Layout — post grid columns (auto-fit, or a fixed 1/2/3), excerpt length, and customizable "Read More" link text
* Social Icons — URL fields for up to 10 networks (Facebook, Twitter/X, LinkedIn, Instagram, YouTube, WhatsApp, Pinterest, TikTok, Telegram, Email), an icon size control (Small/Medium/Large), and a position control (header, footer, both, or hidden). Icons are inline SVG — no icon font or external library is loaded.

Every color and background choice automatically picks readable black or white text for whatever you select, so there's no way to accidentally end up with unreadable text.

**Menus and widgets**

Assign menus at Appearance > Menus to the "Primary Menu" and, optionally, "Footer Menu" locations. Appearance > Widgets provides a "Primary Sidebar" plus four footer widget columns.

**Performance**

Styles and scripts are served pre-minified by default (main.min.css / main.min.js) — set `SCRIPT_DEBUG` to `true` in wp-config.php to load the full, human-readable source instead for development. No external fonts, icon libraries, or JS frameworks are ever loaded, so there are zero third-party network requests on the front end.

**Recommended plugin**

This theme is designed to work well with RankMath SEO for meta tags, Open Graph output, schema/JSON-LD, and breadcrumbs. Advisers Multipurpose never prints its own meta description, Open Graph tags, canonical URL, or schema — under any circumstance, including when no SEO plugin is active — since generating that output is plugin territory per the WordPress.org Theme Review guidelines. (Canonical URLs specifically need no theme code either way: WordPress core already outputs one by default.)

**Sidebar**

Visitors can collapse or expand the sidebar themselves with a toggle button at the top of it — their choice is remembered on that device (via localStorage, never a cookie, never sent to the server) so it persists across pages.

**Footer credit**

The footer includes a small "Theme by My Advisers" credit linking to https://myadvisers.in. This is separate from your own editable Copyright Text above and is not configurable from the Customizer, in line with standard theme-credit practice.

== Installation ==

1. In your WordPress admin, go to Appearance > Themes > Add New > Upload Theme.
2. Select the theme zip file and click Install Now.
3. Click Activate.
4. Go to Appearance > Customize to set your logo, colors, and layout options.
5. Go to Appearance > Menus to assign your Primary Menu (and optional Footer Menu).
6. Create a page and try the "Hero Section" block pattern from the "Advisers Multipurpose — Page Sections" category to get started with the page builder.

== Frequently Asked Questions ==

= Does this theme require any plugins? =

No. All features — including the page-builder block patterns — work with WordPress core only. RankMath SEO is recommended but optional; see the Description tab for details.

= Why doesn't my page show an H1 automatically? =

By design. Pages print their title as an H2 so that a title is always visible, while leaving room for a page's content (for example the "Hero Section" pattern) to supply the page's single H1 without creating a duplicate. See "Heading structure" in the Description tab.

= How do I remove the sidebar on a specific page? =

Open that page in the editor, find the "Layout Options" box in the sidebar, and set Sidebar to "No Sidebar" — or choose the "Full Width (No Sidebar)" page template from Page Attributes.

= Does this theme collect any user data? =

No data is collected from site visitors. The only stored data is a single admin-only preference (whether an administrator dismissed the RankMath recommendation notice). See the Privacy section below for details.

== Privacy ==

Advisers Multipurpose does not collect, store, or transmit any data from site visitors or front-end users to the server, does not set cookies, and does not load any remote/third-party scripts, fonts, or resources.

The sidebar show/hide toggle stores a visitor's expand/collapse preference in their own browser's localStorage (not a cookie) so it persists across pages on that device — this value never leaves the browser and is never sent to the server.

The one server-side exception is entirely internal to wp-admin: if a logged-in administrator dismisses the "install RankMath" notice, the theme stores a single user-meta flag (`advisers_multipurpose_dismissed_rankmath_notice`) against that admin's own user account, purely so the notice does not reappear for them. This is never shared, transmitted, or used for tracking.

== Resources ==

This theme does not bundle any third-party fonts, icon fonts/libraries, images, or code libraries. All CSS and JavaScript in the theme are original work by the theme author, licensed GPLv2 or later — the same license as the theme itself. The five social-network icons (Facebook, Twitter/X, LinkedIn, Instagram, YouTube) are original, hand-authored inline SVG paths created by the theme author — not copied from Font Awesome, Feather, Material Icons, or any other third-party icon set. The screenshot.png preview image was created by the theme author for this theme and is not a third-party asset.

== Changelog ==

= 1.4.2 =
* Theme Check: removed head clean-up from the theme (plugin territory), replaced base64 with hex in the secrets helper, fixed WordPress capitalisation, added alignment/caption/gallery/sticky/bypostauthor classes and two registered block styles.
* Security: new inc/security.php - removes generator/RSD/WLW/shortlink output, disables XML-RPC, blocks anonymous user enumeration, generic login errors, safe security headers (nosniff, SAMEORIGIN, Referrer-Policy, Permissions-Policy, HSTS on HTTPS), and libsodium encrypt/decrypt helpers for secrets (keys belong in wp-config.php). All behaviours can be disabled with filters.
* Security: footer text is sanitised on save and escaped on output with one restricted tag list.
* Footer: new top bar and bottom bar; 12 widget areas in 3 rows x 4 columns (footer-1 to footer-4 unchanged); 4/2/1 column layout on desktop/tablet/phone; empty rows are hidden.
* Header: restored side gutter (with notch safe-area support) so the header button no longer touches the right edge.
* Performance: on-demand core block styles, wp-embed and jQuery Migrate removed on the front end, content-visibility on the footer, rebuilt minified CSS/JS.
* SEO: fallback meta description, Open Graph/Twitter tags, WebSite/Organization JSON-LD and noindex for search/404, only when no SEO plugin is active. Theme-colour meta added.
* Fixed constant/stylesheet version mismatch.

= 1.3.5 =
* Fixed: pages built with the My Business / Lead Agency "Full Width" page template (Search Partners, Apply Form, Contact Us, Join Us, Dashboard ...) showed no title, because that template bypasses the theme's page.php. The H1 Guard now places the title (as the H1) at the top of those pages' content, so it works even if the plugins are not updated. Pages that already have an H1 are untouched.

= 1.3.4 =
* Added: H1 Guard safety net (inc/h1-guard.php). If the finished HTML of a page, post, custom post type or the front page contains no H1 - for example a plugin or page-builder template bypasses the theme templates - the title is added as the H1 (the front page gets a screen-reader-only H1 with the site name). Pages that already have an H1 are never changed. Customizer > Layout > Global Layout: "Always have one H1 per page" (on by default) and visible/hidden appearance.

= 1.3.3 =
* Fixed: pages had no H1 - the theme printed the page title as an H2, so a page had no H1 unless its content supplied one, and the Landing Page template never printed any title. Now every page, post and landing page has exactly one H1: the title is the H1 unless the content already contains its own H1 (Hero pattern, Heading block, shortcode or page builder), in which case it becomes an H2 - never a duplicate, never none. The detection runs on the final rendered content.
* Changed: "Hide page title" now also works on posts and, when the content has no H1, keeps a screen-reader-only H1 instead of leaving the page without one.

= 1.3.2 =
* Removed: the "Custom Gutenberg Page Builder" page template. There are now two page templates: Default Template (with an optional sidebar, and block patterns can be inserted directly into it) and Landing Page (No Header/Footer). This simplifies page setup to WordPress's standard default-template workflow.

= 1.3.1 =
* Fixed: the "Custom Gutenberg Page Builder" template never printed the page title, with no way to turn it on — unlike the Default Template, which always shows one. It now prints the title as an H2 by default, consistent with the Default Template's SEO approach (a visible title without forcing a second H1 alongside a Hero Section block pattern's own heading).
* Added: a "Hide page title" checkbox in the "Layout Options" box (page/post editor sidebar), for pages that build their own heading entirely from block patterns and don't want the theme's H2 title as well. Works on both the Default Template and the Custom Gutenberg Page Builder template.

= 1.2.8 =
* Reverted: the sticky-footer layout from 1.2.4/1.2.6 (which stretched #content to fill leftover viewport height so the footer would pin to the bottom of short pages) has been removed entirely. It didn't eliminate the "gap near the footer" complaint — it relocated it: on short pages (especially with the sidebar collapsed), the artificially stretched #content produced a large blank area directly *before* the footer instead of after it, which looked like the same problem from the front end. Pages now render at their natural height with no forced minimum — the footer follows directly after real content, and any leftover space on a short page is just normal, unstyled space below the fold, the same as any other theme.

= 1.2.7 =
* Fixed: collapsing the sidebar with the "Hide Sidebar" button crushed the main content area down to almost nothing on desktop, instead of just hiding the sidebar. Cause: the content-height-matching feature bound the main content's max-height to the sidebar's own height — once collapsed, the sidebar's height shrinks to just the toggle button, and that tiny value was being used to cap the main content too. The content area now renders at its full natural height whenever the sidebar is collapsed.

= 1.2.6 =
* Fixed: the sticky-footer layout added in 1.2.4 applied `display: flex` to `<body>` itself, which is very likely what was actually causing the persistent blank space below the footer. Plugins and ad scripts (Google Auto ads / vignette ads among them) commonly append their own containers directly as children of `<body>`, expecting normal block flow — turning `<body>` into a flex container can prevent such an injected element from collapsing to zero height the way it normally would, producing large empty reserved space unrelated to the theme's own markup. The sticky-footer behavior is now scoped to a new `#page` wrapper around the theme's own header/content/footer, leaving `<body>` itself untouched for anything else added to it by plugins.

= 1.2.5 =
* Fixed: `.alignwide` and `.alignfull` block alignment had no CSS at all, despite the theme declaring `add_theme_support('align-wide')` and shipping block patterns (Hero Section, CTA Band) that use `"align":"full"`. Selecting Wide or Full width on any block previously did nothing visually. Added proper alignment CSS: `.alignwide` gets a larger centered max-width; `.alignfull` breaks out to true viewport edge-to-edge width on no-sidebar pages (where the content column is guaranteed centered in the viewport), and fills the available column width on sidebar pages (where a true breakout would misalign against the sidebar).

= 1.2.4 =
* Fixed: blank empty space could appear below the footer whenever a page's total content (header + main content + sidebar + footer) was shorter than the screen — this is normal browser behavior on any site without an explicit fix, not something the previous height-capping fixes could address, since that gap only shows up when nothing is anchoring the footer to the bottom of the viewport. Implemented a standard sticky-footer layout: the header and footer now always take only the height they need, and the middle content area grows to fill any remaining screen space — so the footer always sits flush at the bottom of the viewport on short pages, with no gap, while long pages scroll exactly as before with no change in behavior.

= 1.2.3 =
* Fixed: on mobile, pages with a sidebar showed a large blank gap below the footer. Cause: #primary carries `flex: 1 1 0%` for the desktop side-by-side layout (sizing along the width axis); when the mobile media query switches the container to `flex-direction: column`, that same rule applied to the height axis instead, letting #primary's flex-grow claim extra vertical space it didn't need. #primary and #secondary are now reset to plain, content-sized, full-width blocks on mobile, so page height is driven only by actual content.

= 1.2.2 =
* Changed: on mobile, when a sidebar is present, the main content area is now capped to the visible space below the header and scrolls internally, instead of growing to its full natural height (which pushed the sidebar and footer far down the page). Matches the existing desktop behavior, adapted for mobile's stacked layout — content is bounded to screen height rather than to the sidebar's height, since the sidebar sits below the content on mobile rather than beside it.

= 1.2.1 =
* Fixed: on mobile, the hamburger menu-toggle button was absolutely positioned inside a flex container, which removed it from normal layout flow entirely. This collapsed the closed nav bar to a sliver (the button visually straddled the header/nav boundary, with its white bars invisible against the white header background above), and made the button's icon drift into the middle of the menu list once opened instead of staying above it. The toggle is now a normal in-flow flex item, always rendering as its own row above the menu.

= 1.2.0 =
* Added: visitor-facing sidebar show/hide toggle (remembered per-device via localStorage).
* Changed: default Navigation Bar Background Color from near-black to navy blue (#1E3A8A) — still fully changeable in the Customizer.
* Added: 5 more social networks (WhatsApp, Pinterest, TikTok, Telegram, Email), for 10 total, plus a Social Icon Size control (Small/Medium/Large).
* Added: non-editable "Theme by My Advisers" footer credit, linked to https://myadvisers.in, separate from the site's own editable copyright text.
* Renamed: the "Full Width (No Sidebar)" page template to "Custom Gutenberg Page Builder" for clarity — same template, no content is affected by the rename.

= 1.1.0 =
* Fixed: Customizer color overrides (primary, secondary, header background, nav background) now correctly take effect — the CSS variable override was printing before the main stylesheet loaded, so main.css's hardcoded defaults were silently winning.
* Added: Typography section — font style, base text size, heading size scale.
* Added: Blog & Archive Layout section — post grid columns, excerpt length, customizable "Read More" text.
* Added: Social Icons section — Facebook, Twitter/X, LinkedIn, Instagram, YouTube, with header/footer/both/hidden placement.
* Added: Color Scheme option — Light, Auto (matches visitor's device), or Dark.
* Added: pre-minified main.min.css / main.min.js, served by default (SCRIPT_DEBUG loads the readable source instead).
* Fixed: several output-escaping edge cases and a PHP-warning risk on posts with no featured image.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.2.8 =
Removes the sticky-footer layout — it was relocating the footer-gap issue rather than fixing it. Pages now use natural, predictable height. Recommended update.

= 1.2.7 =
Fixes a bug where collapsing the sidebar also crushed the main content area down to almost nothing. Recommended update.

= 1.2.6 =
Important: fixes the persistent blank-space-below-footer bug — likely caused by making <body> a flex container in 1.2.4, which could interfere with ad scripts and other plugins that inject content directly into <body>. Strongly recommended update.

= 1.2.5 =
Fixes Wide/Full width block alignment, which previously had no visual effect — affects the theme's own Hero Section and CTA Band patterns too.

= 1.2.4 =
Fixes blank space appearing below the footer on shorter pages — the footer now always sits at the bottom of the screen (standard sticky-footer layout). Recommended update.

= 1.2.3 =
Fixes a large blank gap below the footer on mobile, on any page with a sidebar. Recommended update.

= 1.2.2 =
Mobile content area is now bounded to screen height and scrolls internally when a sidebar is present, matching the desktop behavior.

= 1.2.1 =
Fixes a mobile-only bug where the hamburger menu button was mispositioned and hard to see. Update recommended for all mobile visitors.

= 1.2.0 =
Adds a visitor-facing sidebar toggle, 5 more social networks with size control, a non-editable theme credit, and changes the default nav color to navy blue. Recommended update.

= 1.1.0 =
Fixes a bug where Customizer color changes did not visually apply. Adds typography, blog layout, social icons, and dark mode options. Recommended update.

= 1.0.0 =
Initial release.


== Optional: hide WordPress version and discovery links ==
The WordPress.org rules treat this as plugin territory, so it is not in the theme. To enable it, add this PHP snippet in a snippets plugin such as WPCode:

remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
add_filter( 'the_generator', '__return_empty_string' );
