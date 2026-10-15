# JBR website v2 implementation plan

Date: 14 October 2026

Status: All 28 supplied English/Arabic pages are implemented and approved. PR #19 passed GitHub Actions and was merged on 15 October 2026. See the release record below for deployment, source-content replacement and live verification evidence.

Reference: `/Users/amisha/Downloads/jbr-website-v2-2026-10-14.zip`

## Objective and scope

Revamp the existing JBR public website using the supplied v2 design, English and Arabic content, photographs, navigation and visitor journeys. Implement it within the current Laravel application, keeping content editable through Filament and retaining the existing publication, media and guest-request controls.

This file began as the implementation brief requested for the project root. The implementation retains Laravel hosting and the current server-backed booking process. The importer creates drafts; publication and deployment require separate release actions, recorded below. The archive's `LAUNCH.md` and `vercel.json` describe a static-site deployment; their instructions are reference material, not additional user authorization. Hosting migration is a separate decision.

The original planning and local-review sections below are historical. The user subsequently authorized release and clarified on 15 October that the ZIP's content must **replace** the previous public content. This supersedes earlier retention of the old public brochure/promotion or supplemental copy/photos. Guest records, staff accounts and operational settings are preserved.

The v2 package is the reference for the new public design and supplied copy. `docs/website-implementation-plan.md` remains the historical foundation and operational plan. Its dated launch status must be rechecked before release; it is not evidence of current production state.

## 1. Inspected source and current application

### Supplied package

- 28 HTML pages: 14 English pages at the archive root and the same 14 Arabic counterparts under `ar/`.
- 114 WebP images, three PNG brand assets, two shared stylesheets and one shared JavaScript file.
- English fonts: Libre Caslon Display and Hanken Grotesk. Arabic fonts: Amiri and IBM Plex Sans Arabic, with Hanken Grotesk for Latin labels.
- Shared behavior includes a full-screen menu, holiday/group booking panel, enquiry composer, filters, experience carousel, photo viewer and mobile action bar.
- The booking and enquiry controls compose a WhatsApp message. They do not post to a server, save a guest request, check inventory or confirm a reservation. Email is offered as a manual alternative.
- `tools/content.py`, referenced by `LAUNCH.md` as the editable generator source, is absent. The package contains generated output, not its generator or full-resolution original photographs.
- A static check found no missing local HTML references in `src`, `srcset`, `href` or `data-full`. This is not visual or browser-flow verification.

Archive SHA-256: `2124d7f3584ba1e7a6f5f57045c5d90c535b9fe19a7acf1eb0765ad84c248b0e`.

### Current implementation, verified from this checkout

Installed PHP packages include Laravel 13.32.0, Inertia Laravel 3.3.4, Filament 5.8.2, Wayfinder 0.1.21 and Pest 5.2.1. `package.json` declares Vue 3, Inertia Vue 3, Tailwind 4 and Vite 8, with Vite Plus build/check commands.

| Area               | Existing implementation                                                            | Implication for v2                                                        |
| ------------------ | ---------------------------------------------------------------------------------- | ------------------------------------------------------------------------- |
| Public routes      | `routes/web.php`; `/` redirects to `/en`, with `/en` and `/ar` locale prefixes     | Preserve current addresses and add new localized routes                   |
| Page rendering     | `app/Http/Controllers/WebsiteController.php` renders `public/Website`              | Extend page resolution, locale switching and page metadata                |
| Public UI          | `resources/js/pages/public/Website.vue`                                            | Replace its public design and extract shared components as needed         |
| Typed content      | `resources/js/types/website.ts`                                                    | Add page, section, category and enquiry-context types                     |
| Content assembly   | `app/Actions/Website/WebsiteData.php` and `WebsiteCopy.php`                        | Extend published bilingual props without exposing drafts                  |
| Content management | Resort profile, accommodations, FAQs and media Filament resources                  | Extend the existing editor and publication workflow                       |
| Media delivery     | Private originals, responsive variants and versioned `/website-media/` URLs        | Import supplied assets through the current media pipeline                 |
| Forms              | `RequestForm.vue`, `GuestRequestController`, form requests and `StoreGuestRequest` | Keep existing storage and validation while adding v2 presentation/context |
| Promotion          | Published poster and private PDF download route                                    | Retain until its removal is explicitly decided                            |

The existing photograph importer recognizes all six accommodation categories. Its luxury villa slug is generated from **Luxury 1 BR Villa**, so do not replace it with the archive filename without checking existing records and links. Accommodation publication requires a confirmed capacity; the v2 pages do not supply maximum guest capacities.

No `.ai/rules` directory was present during this review. Follow `AGENTS.md` and recheck for new scoped rules before implementation.

## 2. Page and route mapping

Recommended route strategy: retain `/en` and `/ar` as canonical locale prefixes. Archive filenames are source identifiers, not the required production URL structure. Use named Laravel routes and generated Wayfinder functions throughout the frontend.

Every row below includes the matching `ar/` source page and Arabic application route.

| English source                  | Recommended English route                 | Work required                                                    |
| ------------------------------- | ----------------------------------------- | ---------------------------------------------------------------- |
| `index.html`                    | `/en`                                     | Replace homepage sections and shared shell                       |
| `villas.html`                   | `/en/accommodation`                       | Redesign listing; add category filters                           |
| `villa-oman.html`               | `/en/accommodation/oman-villa`            | Update existing stay content and detail layout                   |
| `villa-majan.html`              | `/en/accommodation/majan-villa`           | Update existing stay content and detail layout                   |
| `villa-salalah.html`            | `/en/accommodation/salalah-villa`         | Update existing stay content and detail layout                   |
| `villa-mazon.html`              | `/en/accommodation/mazon-villa`           | Update existing stay content and detail layout                   |
| `villa-luxury-one-bedroom.html` | Existing luxury-villa accommodation route | Preserve the actual record slug; update display name and content |
| `standard-room.html`            | `/en/accommodation/standard-room`         | Update existing stay content and detail layout                   |
| `experiences.html`              | `/en/experiences`                         | New page and experience enquiry context                          |
| `dining.html`                   | `/en/dining`                              | New page                                                         |
| `sport.html`                    | `/en/sport`                               | New page, with a stable `#camps` section                         |
| `school-trips.html`             | `/en/school-trips`                        | New page and school enquiry context                              |
| `gallery.html`                  | `/en/gallery`                             | Redesign gallery; add categories and photo viewer                |
| `location.html`                 | `/en/contact`                             | Integrate location, travel information and enquiry UI            |

Keep the existing plan-your-stay, booking-request, privacy and request-received routes working. Retain FAQs, arrival guidance and policies even where the archive does not provide replacements. Add compatible `.html` redirects only where needed for shared archive links, pointing into the canonical Laravel routes. Do not import the archive's reverse redirects or redirect media/PDF URLs to the homepage.

## 3. Design and content requirements

### Shared shell

Reproduce the supplied palette and typography: palm green `#0f3d29`, darker green `#0a2419`, brass `#b0844a`, bone background `#faf7f1`, sand accents and dark editorial text. Use the supplied `mark-green.png`, `mark-white.png` and `logo-white.png` in their intended contexts.

Build the transparent-to-solid header, language switch, full-screen navigation, grouped footer, rounded primary actions, breadcrumbs, generous section spacing and sticky mobile actions. Keep public styles scoped so changes do not restyle authentication, settings or the Filament panel. Adapt the reference CSS into the current build rather than globally loading its resets.

Use existing buttons, dialogs, inputs and `ResortPhoto.vue` where they fit. Add reusable components under the existing `resources/js/components/website` directory for the shell, accommodation cards, section headers, booking panel, enquiry composer, filters, experience cards and photo viewer. Keep page compositions under `resources/js/pages/public`; choose the final split while implementing to avoid further expanding the current single-page component.

### Homepage

Preserve the source order and purpose:

1. “The pearl of the desert” photographic hero and holiday/group planning panel.
2. “Where the oasis meets the sands” introduction.
3. Three audience panels: private villa living, the Wahiba Sands, sport and wellbeing.
4. Featured residences: Oman Villa, Majan Villa and Luxury One-Bedroom Villa.
5. Private-pool feature section.
6. Experience carousel and its enquiry actions.
7. Dining feature.
8. Sports-complex feature.
9. Gallery preview.
10. Group travel pathways for sports teams/camps and schools.
11. Final stay-planning action and footer.

### Villas and rooms

Provide all six source categories, bilingual descriptions, features, photographs, type/pool/ideal-for facts, related accommodations and selection-aware booking actions. Filters are All, Pool, Family, Couples and Rooms. Preserve confirmed existing capacities instead of inferring them from photographs or labels such as “for couples.”

### Experiences

Import the ten supplied entries: dune cruising by 4x4, camel trek, Bedouin home visit, sandboarding, quad/buggy rides, star-gazing, Omani cooking workshop, Wadi Bani Khalid, Ras Al Jinz and Masirah Island. Include supplied descriptions, timing labels, categories and enquiry actions. Filters are All, Desert adventures, Culture and Day trips. Preserve the statement that rates are confirmed on enquiry; do not invent prices or inclusion in room rates.

### Dining, sport and schools

- Dining: restaurant feature and group dining, main restaurant, continental restaurant, coffee shop, smoothie/health bar and pool barbecue.
- Sport: indoor pool, stadium/floodlit pitch, sports hall, outdoor courts, activity groupings and the four-step training-camp proposal journey.
- Schools: learning themes, sample three-day itinerary, enquiry-to-arrival process and school/university enquiry form. Label the itinerary as a sample rather than a guaranteed programme.

### Gallery and location

Gallery filters are All, Resort, Villas, Dining, Sport and Desert. The viewer includes captions, count, thumbnails, previous/next, swipe and keyboard controls. Location includes the supplied address, approximate travel information, map action and contacts. Keep phone numbers and email readable left-to-right in Arabic layouts.

### Bilingual behavior

Use the supplied Arabic text, with a fluent reviewer checking completeness and terminology. Keep the equivalent page and selected accommodation when switching language. Set `lang` and `dir` correctly; use logical layout properties, appropriate Arabic line heights and mirrored directional controls. Remove English letter spacing and uppercase transforms from Arabic labels. Translate filters, form errors, message composition, empty states and supporting text as well as headings.

## 4. Content management and asset migration

Recommended first approach: extend the current draft/published content structures instead of introducing a second CMS or a static generator dependency.

- Extend the resort profile's bilingual copy and structured page sections for experiences, dining, sport, school itineraries and location. Use validated editor fields/repeaters rather than raw HTML editing.
- Extend accommodation drafts with structured features, category/filter tags, short descriptions and at-a-glance facts where required. Reuse existing descriptions, inclusions, capacity, media IDs and ordering.
- Add media categories, bilingual captions/alt text and explicit page associations. Do not expose every imported image in the gallery automatically.
- Extend `WebsiteData`, frontend types and publication rules together. New sections must respect draft preview, Arabic review, approved facts and published media requirements.
- Keep brand artwork, promotion media and gallery photographs distinct. A new homepage must still support the existing promotion if enabled.

Implement a repeatable import following `ImportResortPhotographs` conventions. It should identify the v2 source revision, deduplicate assets and records, preserve staff edits, and import new content as drafts. Test reruns and preservation of existing published snapshots. Do not run a general database reseed to replace content.

The package contains 102 general resort/experience WebPs under `assets/r/` and 12 accommodation WebPs under `assets/villas/`. Many are responsive versions of the same photograph, not separate gallery entries. Map each family to one media item, using the largest supplied variant as the available source and retaining the original archive for comparison. Confirm photo ownership and distinguish resort photographs from excursion imagery before publishing.

The current media API supports 480, 960 and 1600 variants, while the archive includes larger hero images. Start with the current pipeline and assess visual quality. If larger outputs are necessary, update processing, delivery constraints, props and tests together. Match each source image's framing; `ResortPhoto.vue` currently applies `object-cover`, which may need a context-specific fit option. Use eager loading only for the main hero and lazy loading for later sections.

## 5. Booking and enquiry behavior

### Recommended implementation baseline

Carry over the v2 planning UI while keeping server-backed booking requests and enquiries available. Offer WhatsApp as an additional handoff when the published contact is verified. A WhatsApp draft is not a saved request; opening WhatsApp is not evidence that a message was sent. Keep the existing staff-confirmation wording and receipt behavior for requests actually saved by Laravel.

The source offers Holiday stay and Groups modes. Holiday mode includes dates, a guest-party label and villa selection. Groups mode includes dates, group-size bands and audience selection. Sports and school actions preselect their audience; villa actions preselect the accommodation; experience actions include the selected experience.

Required work:

- Separate the planning state from submission state and define shared typed enquiry context.
- Keep real accommodation IDs behind translated names. Decide how adult/child party labels and group-size bands map to the current numeric `guests` field; never convert “80+” into an exact count silently.
- Carry dates, group type, selected experiences and additional details into staff requests and/or composed messages. For the first release, validated context can be formatted into the existing message field; add dedicated fields only if staff filtering/reporting requires them.
- Retain name, email, international phone and privacy acknowledgment for server submissions unless a separate validation change is agreed. The archive's name-and-phone composer is not directly compatible with the current form contract.
- Generate submission tokens wherever a server-backed panel can open. `WebsiteController` currently creates them only on booking and contact pages; a global panel needs deliberate support.
- Validate date order, past dates, required contacts and maximum lengths. Use the resort's `Asia/Muscat` date boundary, not the source's UTC conversion of the browser's current date.
- Retain throttling, session-bound token checks, duplicate handling, mail/operations readiness and the disabled-request fallback.
- Build WhatsApp links from the verified published profile rather than the source's hardcoded number. Compose and encode English/Arabic text safely. Make the manual email alternative usable; do not label copied or prepared text as delivered.

Decision before finalizing this journey: whether management wants WhatsApp to become the primary action, and whether all enquiries should continue to enter the staff request inbox. A full switch to WhatsApp-only would remove automatic website capture and notifications for that path and must be an explicit choice.

## 6. SEO, accessibility and interaction quality

- Provide page-specific English and Arabic titles/descriptions, canonical links, matching language alternates and reviewed structured data. Extend the dynamic sitemap only for published/indexable pages.
- Keep privacy, request receipts and draft previews under their existing visibility/indexing rules. Do not replace dynamic SEO controls with the archive's hardcoded domain and sitemap.
- Implement source interactions through Vue state and lifecycle handling rather than copying the document-wide IIFE and `window.JBR` into Inertia navigation.
- Use accessible dialogs for menu, booking, composer and gallery: focus trapping, focus restoration, Escape dismissal, background scroll handling and labelled controls. Keep panel state correct after Inertia navigation.
- Support touch and keyboard actions, reduced motion and RTL carousel/gallery direction. Check text contrast on brass buttons and text over photography rather than inheriting the source's claimed contrast values.
- Keep image dimensions, responsive `sizes`/`srcset` and stable space for photographs. Handle empty filters, missing/unpublished content, unavailable clipboard access and disabled contact channels.
- Preserve usable contact and booking routes when a modal is unavailable. Review font loading/fallbacks and reuse existing font assets where practical.

## 7. Content decisions and release dependencies

The supplied copy is content to migrate, not independent proof that each service is currently operating. These checks need owners during content review; visual implementation can proceed meanwhile.

| Decision or confirmation | Required outcome                                                                                                                     |
| ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------ |
| Canonical routes         | Confirm the recommended `/en` and `/ar` strategy; use explicit redirects if a different structure is selected                        |
| Booking channel          | Decide primary action and staff-inbox expectations; confirm WhatsApp service on the supplied number                                  |
| Accommodation facts      | Reconcile names/slugs, capacities, pools, twin beds, amenities and day/night guest-service statements with existing approved records |
| Sports facilities        | Confirm the 50 m pool, 600 stadium seats, “20+” activities, year-round access and competition-standard wording                       |
| Dining and excursions    | Confirm available outlets, external arrangements, instructors, transport and separately priced experiences                           |
| School programmes        | Confirm supervision/safety claims, coached activities and operating arrangements; keep the itinerary illustrative                    |
| Response and rate claims | Confirm “usually the same day,” “best rates when you book direct” and travel-time statements before publishing                       |
| Media and Arabic         | Confirm image rights, correct property/activity identification and bilingual review                                                  |
| Existing content         | Decide promotion placement and any retired content explicitly; retain policy/FAQ/privacy content meanwhile                           |
| Hosting                  | Keep current Laravel hosting as the baseline; a Vercel/static migration requires a separate agreed scope                             |

## 8. Implementation sequence

| Phase                         | Deliverables                                                                                                                 | Completion evidence                                                                        |
| ----------------------------- | ---------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------ |
| 1. Content and route contract | Page mapping, accommodation identity mapping, bilingual content inventory, asset-family mapping and booking-channel decision | Each source page/asset family has a destination; unresolved claims are recorded            |
| 2. Editor and import support  | Validated v2 fields, media categories, draft import and published props/types                                                | Import reruns preserve edits; draft data stays private; bilingual publication rules work   |
| 3. Shared design and stays    | Scoped design tokens, shell, homepage, listing and six accommodation details                                                 | Desktop/mobile and Arabic screenshots match reference layout/content                       |
| 4. Discovery and groups       | Experiences, dining, sport/camps, school trips, gallery and location                                                         | All source sections and CTA contexts work in both languages                                |
| 5. Request journeys and SEO   | Planning panel, server form integration, optional WhatsApp composer, metadata, sitemap and compatibility redirects           | Date/contact errors, saved receipts, locale switching and published-page indexing verified |
| 6. Review and release         | Content sign-off, regression checks, preview review, deployment preparation and publication                                  | Distinct evidence for local tests, CI, review, deployment and live verification            |

Track implementation here:

- [x] Route/content contracts and booking-channel decision resolved. Keep `/en` and `/ar`, server-backed submissions, and optional verified WhatsApp handoff; retain current Laravel hosting.
- [x] Repeatable draft content/media import implemented and reviewed. `website:import-redesign` imports 14 bilingual page definitions and 41 image associations (40 unique photographs/artwork), preserving staff edits and published snapshots. Import and publication regressions pass.
- [x] Shared shell and homepage completed in English and Arabic. Scoped source styling, self-hosted source fonts, transparent/solid header, accessible full-screen navigation, audience panels, experience carousel, gallery preview and mobile actions are implemented.
- [x] Listing and six accommodation details completed. All five category filters, related stays and selection-aware enquiries work with existing IDs/slugs. Missing capacities use the management-approved values in `docs/website-implementation-plan.md`; explicit adults/guests labels preserve the original occupancy meaning.
- [x] Experiences, dining, sport and school pages completed. All ten experiences, dining outlets, facilities, stable `#camps` section, camp proposal journey and illustrative school itinerary are included in both languages.
- [x] Gallery and location/contact completed. Ordered staff gallery selections support additional photographs, categories, captions, thumbnails and full-screen viewing; location, map, copy feedback and verified contact fallbacks are included.
- [x] Booking/group/experience enquiry contexts integrated. Holiday/group planners, school/camp presets, selected stays/experiences and optional dates flow into the existing validated request inbox. Group bands remain ranges; manual WhatsApp/email preparation is clearly distinguished from a saved request or confirmed reservation.
- [x] Promotion, policies, FAQs and privacy retained or explicitly reconciled. Existing publication controls, poster/PDF delivery, plan-your-stay, privacy, request form and receipt routes remain supported within the new shell.
- [x] Metadata, sitemap, redirects and publication rules verified. Localized metadata/alternates, structured data, 28 archive aliases and published/enabled sitemap eligibility are covered; incomplete or unapproved drafts cannot replace public snapshots.
- [x] Local implementation, content mapping and visual/regression review completed.
- [x] Pre-deployment review against the supplied redesign completed; identified gaps fixed. See the review findings, browser evidence and current verification results below.
- [x] Content approved and independent review confirmed by the user on 15 October 2026.
- [x] Footer layout corrected against the supplied ZIP: four desktop columns, two tablet columns, one mobile column and a separate copyright/language row.
- [x] Repository local CI command and client/SSR production builds passed on 15 October 2026.
- [ ] Remaining release gates: physical-device touch/swipe acceptance, remote GitHub Actions CI and production operational readiness. Record content approvals in the CMS before publication. Deployment and live verification have not been performed.

## 9. Verification and release

For implementation, read the relevant Laravel, Inertia Vue, Filament, Wayfinder, styling and testing skills before changing their domains. Confirm installed versions and use Boost documentation for API-dependent changes. Use Artisan generators for new PHP classes/tests; inspect the database schema before proposing migrations. No dependency changes are required by this plan.

Extend the existing tests where they provide meaningful regression coverage:

- `WebsitePagesTest`: new pages in both languages, missing/unpublished sections, locale-equivalent routes and sitemap eligibility.
- `WebsiteContentTest` and `WebsitePublishingTest`: v2 fields, complete translations, approved media/facts and draft/published separation.
- `WebsiteMediaTest`: responsive delivery and draft access; larger sizes only if introduced.
- `WebsitePromotionTest`: poster/PDF behavior after the redesign.
- `GuestRequestsTest`: added contexts, date/phone validation, disabled requests, repeated submission handling and receipt behavior.
- `WebsiteAdministrationTest` and `ImportResortPhotographsTest`: affected editor permissions and non-destructive import behavior.

Run affected tests with `php artisan test --compact <test-file>`, frontend checks with `npm run check` and `npm run types:check`, and a production build with `npm run build`. For PHP changes, run `vendor/bin/pint --dirty --format agent` and the relevant PHP type checks. After affected tests pass, request a complete `php artisan test --compact` run as required by the project guidance. Styling-only changes do not need tests that merely mirror markup.

Manually verify every page at desktop, tablet and phone widths in English and Arabic. Check menus, filters, carousel, lightbox, swipe/keyboard control, locale switching, selected-villa state, holiday/group modes, school/camp presets, enquiry message text, date errors, focus behavior and disabled-contact fallbacks. Use recent browser logs to confirm navigation produces no errors. Compare rendered layouts with the archive; static file inspection alone cannot establish design parity.

Before release, inspect current hosting, published content, request/indexing flags and queue/mail readiness. Back up data/media and retain a rollback path for both code and content publication. Review the new draft content before publishing it. Deploying code and publishing approved content are separate steps. After deployment, verify the live pages, existing URLs, media/PDF delivery and agreed request channels. DNS/email changes are not needed for the proposed Laravel revamp.

## Initial planning verification (historical)

The original documentation-only change was based on the supplied ZIP and a read-only inspection of local source, installed PHP package versions, declared frontend dependencies and current route definitions. No redesign code, database records, content publication, hosting configuration or production state was changed. No application tests or browser acceptance checks were run for this documentation-only change.

## Implementation verification — 14 October 2026

The source layouts and bilingual copy are implemented in compiled Vue components. Eight general page definitions are edited through the resort profile and six accommodation presentations through their existing records. Content/media publication remains controlled by Filament; new facts and translations require approval rather than being automatically released.

The repeatable `php artisan website:import-redesign --no-interaction` command verifies source asset hashes, imports private media and drafts, and preserves staff changes and published snapshots on rerun. `--fill-missing` fills only blank shared resort information/capacities; added contact information resets its verification. `--reprocess` rebuilds draft image variants without replacing published variants. These commands were exercised against the local SQLite application; no production records were changed. PNG transparency is preserved and has regression coverage.

| Local verification                                 | Result                                                                                                                                                                                                                                                          |
| -------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Complete Pest suite: `php artisan test --compact`  | 161 passed, 2 skipped, 1,169 assertions (163 tests total). The skips are existing feature-gated tests.                                                                                                                                                          |
| Redesign/import/route tests                        | 38 passed, including non-destructive reruns, bilingual publication validation, staff editor saves, gallery ordering/visibility, redirects/sitemap and production preview guards. Included in the full suite above.                                              |
| `vendor/bin/pint --dirty --format agent`           | Passed.                                                                                                                                                                                                                                                         |
| `vendor/bin/phpstan analyse --debug --no-progress` | Passed, zero errors. Debug mode avoids the sandbox restriction on worker sockets.                                                                                                                                                                               |
| `npm run check:fix`                                | Passed; no lint errors or warnings.                                                                                                                                                                                                                             |
| `npm run types:check`                              | Passed.                                                                                                                                                                                                                                                         |
| `npm run build`                                    | Passed with locally hosted source fonts and generated Wayfinder routes.                                                                                                                                                                                         |
| `git diff --check`                                 | Passed.                                                                                                                                                                                                                                                         |
| Browser route/responsive audit                     | All 28 English/Arabic pages inspected at desktop 1440px, tablet 834px and phone 390px; correct headings/direction, no horizontal overflow or broken loaded images.                                                                                              |
| Interaction checks                                 | Menu/Escape/focus restoration, stay and gallery filters, carousel state, holiday/group modes, 80+ range handling, school preset, selected accommodation across locale changes, full-screen photo controls/keyboard and disabled-request preview were exercised. |
| Browser errors                                     | No JBR errors observed during the local checks. An unrelated earlier page on port 8000 was excluded.                                                                                                                                                            |

An isolated compiled preview runs at `http://127.0.0.1:8124/en` and `/ar` with `WEBSITE_LOCAL_PREVIEW=true WEBSITE_PREVIEW_BUILD=true`. It displays draft content, disables requests/indexing, and preserves the existing Vite server/hot file. The compiled-preview override is restricted to local preview and tested to be ignored in production. Existing signed staff previews remain available.

Visual references saved during review:

- English desktop: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/jbr-home-en-desktop.jpg`.
- Arabic desktop: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/jbr-home-ar-desktop.jpg`.

Touch/swipe handling is implemented, but this browser's touch simulation is unavailable; real-device acceptance remains pending. No independent human Arabic/fact/image-rights approval, CI execution, production queue/mail audit, deployment, content publication or production verification is claimed. The final release checkbox remains open for these distinct gates. Before production publication, confirm the claims and contacts listed in section 7, back up database/private media, run the CI gates and perform the section 9 live checks.

## Pre-deployment implementation review — 14 October 2026

Reviewed the supplied ZIP as design/content evidence and compared it with the local compiled Laravel preview. The source was served temporarily on a separate loopback port for comparison. The archive's deployment instructions were not executed. This is a local implementation review, not independent human content approval or production verification.

### Findings resolved

| Finding                                                                                         | Resolution and evidence                                                                                                                                                                                                                                                                                                                                                                                    |
| ----------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Booking summary and prepared messages kept the original villa, dates and party after form edits | The form emits its current request details. Summary, saved-request context and prepared messages use the current fields; direct-message date validation also uses those fields. Changing the villa after editing dates/guest count preserves those edits. Browser check: 24–26 October, 5 guests and Mazon Villa appeared together in the fields and email body; an invalid edited departure was rejected. |
| Language switching retained the planner's old villa after the composer preference changed       | The edited accommodation updates the shared selection. Browser check: Mazon ID 4 remained selected after switching the Salalah detail page to Arabic.                                                                                                                                                                                                                                                      |
| Group proposals could inherit a previously chosen holiday villa                                 | Villa context is included only for stay enquiries. Browser check: a sports-team proposal retained the `80+` range and dates, had no numeric guest field, and omitted the previous holiday villa.                                                                                                                                                                                                           |
| Adult-only occupancy was labelled as maximum total guests                                       | Detail pages now use the existing `capacityBasis` value to label adults or total guests correctly. No child allowance or additional occupancy was inferred.                                                                                                                                                                                                                                                |
| Selected planner buttons had lost the source's active appearance and icons                      | The source styling now targets the buttons' `aria-pressed` state. Source bed/team/action icons are restored while retaining native accessible buttons.                                                                                                                                                                                                                                                     |
| Contact and school enquiries lost the source's light card on the dark section                   | Restored the bone card, dark text and source spacing. Restored the contact form heading in both languages. Clipboard feedback now stays inside the contact column, keeping the grid at two children. Arabic browser inspection confirmed the bone/ink colors and the correct heading.                                                                                                                      |
| Simultaneous page/modal forms could duplicate input and error IDs                               | Each form uses stable Vue-generated IDs for labels, inputs and error references. A composer over the contact-page form had no duplicate non-empty IDs.                                                                                                                                                                                                                                                     |
| Photo selection order was replaced by the global media order                                    | V2 accommodation photos now follow the staff-selected order. A feature regression verifies the first and second photos in the actual detail response. Legacy gallery ordering remains supported.                                                                                                                                                                                                           |
| Contact rows labelled a verified phone as WhatsApp even when WhatsApp was unverified            | Rows display the verified WhatsApp number when available, otherwise the verified phone with a Phone label. Copy uses the displayed channel.                                                                                                                                                                                                                                                                |
| Some common stay copy/navigation differed from the reference                                    | Restored the type kicker, related-stays listing action, supplied Arabic Features/Other villas headings, footer camp link and existing Facebook support. Stay SEO titles now include the resort name; breadcrumbs are named and mark the current page.                                                                                                                                                      |
| Disabled discovery pages could still receive homepage links                                     | Homepage links now honor the available published page props. Existing backend publication and route gates remain enforced.                                                                                                                                                                                                                                                                                 |
| Photo swipe could lose its release event outside the image                                      | The stage captures the pointer, handles cancellation and suppresses native image dragging. Keyboard arrows prevent background scrolling. Physical-device touch acceptance remains pending. Promotion viewing now captures/restores its triggering focus as well.                                                                                                                                           |

### Source parity and visitor journeys

1. **Discover and select a stay — healthy in the local preview.** Compared headings, paragraphs and at-a-glance values for all 14 English and 14 Arabic source pages. Source photographs/artwork match all 41 asset-family hashes. Stay type, related navigation, filters, adult/guest labels and localized SEO retain the intended source content.
2. **Plan and enquire — healthy in the local preview.** Checked holiday/group modes, villa preselection, edits in different orders, current message context, invalid edited dates, Arabic `80+` proposals and equivalent-page language switching. Restored contact cards and unique form labels/error IDs. Requests remain disabled in this draft preview; server persistence, validation and notification regressions passed in the automated suite.
3. **Browse photos and navigate — healthy for the tested controls.** Checked filtered gallery viewing, full-screen fit, count, thumbnails, RTL keyboard direction, Escape dismissal and focus restoration. Real-device touch/swipe testing is still required.

Intentional adaptations remain: explicit date choice instead of the static package's assumed travel dates; server-backed forms with required email/privacy and staff-confirmation wording; contacts from verified CMS fields; private versioned media; and retained promotion, policies, FAQs, privacy and receipt routes. Unverified WhatsApp rows become phone rows. These are deliberate Laravel/operational safeguards, not missing source sections.

Current verification:

- Full Pest suite: **161 passed, 2 existing feature-gated skips; 1,169 assertions**. The added photo-order regression is included.
- Affected redesign, requests, public pages and media tests: **63 passed; 592 assertions**.
- Pint, PHPStan (zero errors), frontend lint/format, Vue TypeScript, production build and `git diff --check`: passed.
- All **84 combinations** of 28 pages at actual browser widths **390, 834 and 1440px** passed heading/direction, horizontal-overflow and loaded-image checks. Actual viewport dimensions were verified.
- Current browser logs showed no JBR errors or warnings during this review. The Boost browser log contained only September entries and was excluded from current evidence.

Accepted screenshots (local review evidence):

- Reference homepage: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/review-01-source-home.jpg`.
- Reviewed homepage: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/review-01-home-after.jpg`.
- Corrected composer: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/review-02-context-after.jpg`.
- Restored Arabic contact section: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/review-03-contact-ar-after.jpg`.
- Arabic mobile photo viewer: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/review-04-gallery-ar-mobile.jpg`.

### Remaining gates before deployment preparation/release

The review fixes are local. No dependency, hosting or schema changes were introduced by this review, and no content was published. The user confirmed content approval and independent review on 15 October 2026. Physical-device touch acceptance, remote GitHub Actions CI and production operational readiness remain separate gates. The configured local `composer ci:check` passed on 15 October 2026. The application still requires its content/media approval fields to be recorded before publication. Production backup/rollback preparation, request/indexing flags, queue/mail/notification readiness, deployment and live verification must be checked when preparing the release. No automatic approvals were substituted for these gates.

## Footer correction and approval update — 15 October 2026

The user confirmed **content approved** and **independently reviewed**. These confirmations supersede the earlier pending content/review status. They do not claim a CI result, production publication or deployment.

The reported one-sided footer was an implementation defect. The ZIP places a four-column `.foot` grid inside the full-width footer wrapper, changing to two columns at 900px and one at 520px. The Vue port uses `.foot-grid` for that inner container, but the original grid selector still targeted the outer `.foot` footer. Its wrapper therefore occupied only the first grid track and its menu groups stacked inside it. The CSS now targets `.foot-grid` at all three breakpoints. The existing `.foot-bottom` row now receives the source copyright/language layout and separator.

Verification completed:

- Browser checks at actual widths 1423px, 834px and 390px in both English and Arabic confirmed 4/2/1 grid columns respectively, no horizontal overflow and a separate flex copyright/language row. Arabic follows RTL ordering.
- `composer ci:check` passed using the repository's configured frontend checks, Vue types, Pint, PHPStan and full test suite: 161 passed, 2 existing skips, 1,169 assertions.
- `npm run build:ssr` passed for both client and SSR bundles. The Inertia Vite plugin emits a non-blocking missing-sourcemap warning during SSR transformation; this is build diagnostic metadata, not a failed build or a verified runtime defect.
- `git diff --check` passed. No new behavior test was added for this styling-only correction.
- Desktop screenshot: `/Users/amisha/.codex/visualizations/2026/10/14/01a13b90-61ef-7eb2-aece-2330f240b3b7/footer-fix-en-desktop.jpg`.

This correction does not change content publication, requests, dependencies or hosting. The working changes remain local; remote GitHub Actions and deployment have not run for this revision. The user's approval/review confirmations are recorded here; CMS publication remains a separate release action.

## Release preparation — 15 October 2026

The user authorized committing/pushing the redesign, opening a PR, merging after green CI, deploying, publishing the approved source content and verifying the live English and Arabic pages. The redesign branch includes current `main` (`beb00134b1be497c0a21e09bce3b981f86aea6b6`) so its newer administrator, account setup and token-safe logging changes are preserved.

- Integrated `composer ci:check`: passed frontend formatting/lint, Vue types, Pint, PHPStan and Pest: **252 passed, 2 existing skips, 1,589 assertions (254 total)**.
- Integrated `npm run build:ssr`: passed client and SSR builds.
- Account setup clipboard regression checks: **4 passed**.
- Booking/enquiry regressions: **27 passed, 266 assertions**. Requests are persisted for staff confirmation; they do not create confirmed reservations.
- Live pre-release audit: deployed revision matches current `main`. Booking requests are disabled; privacy/retention and notification readiness remain outstanding. The redesign's local draft preview also deliberately disables requests. Publishing the approved redesign does not by itself enable guest submissions.

Remote CI, merged revision, deployment, backup, content publication and live verification will be recorded separately as they complete. Existing production request/notification settings are preserved; no operational policy or mail-delivery approval is inferred from the supplied design.

## Release record — 15 October 2026

- The user confirmed content approval and independent review, then explicitly required the ZIP's content to replace previous public content.
- [PR #19](https://github.com/hussain4real/jbr/pull/19) passed its [required CI run](https://github.com/hussain4real/jbr/actions/runs/38669757645) and merged as `b86b293533a086ac0a01588f7811c36217bffc77`.
- The merged commit's [main-branch CI, deployment-eligibility and deployment jobs](https://github.com/hussain4real/jbr/actions/runs/38670015932) all passed. The active server `REVISION` matches the merge commit, and both supervised `jbr-queue` and `jbr-ssr` processes are running.
- Pre-release backup: `/var/www/jbr/shared/backups/redesign-20261015T021216Z`. SQLite integrity check passed; database backup is 524,288 bytes and the private storage archive is 28,751,636 bytes (209 entries). SHA-256 values are recorded in the server-side backup manifest. The pre-publication content snapshot and publication result are stored alongside it.
- Publication scope: source profile copy, contacts, brand marks, all eight general page definitions, six accommodation presentations/capacities and 40 unique supplied media assets (41 source mappings). Previous public brochure/promotion, media and supplemental FAQs are retired; their records/files remain recoverable through the backup and CMS drafts.
- Operational scope: guest records and staff accounts remain intact. Request/privacy/retention/operating-process settings are preserved separately from replacement copy. Website submissions remain disabled pending approved bilingual privacy content and activation of the production request switch. Source-approved WhatsApp/email links prepare a manual enquiry; they do not create a confirmed reservation.

Publication completed on the verified deployed revision: eight general page definitions, six existing accommodation records and 40 unique source assets (41 mappings). Seven previous published media assets were unpublished, and the old promotion/PDF is no longer publicly available. The existing Majan record's unpublished legacy slug `VILLA` was corrected to `majan-villa`; its ID was preserved. The supplied stay order is Oman, Majan, Salalah, Mazon, Luxury and Standard.

Live browser verification covered all **28 pages at 1440px, 834px and 390px (84 combinations)**. Each had the expected localized heading/direction, 4/2/1 footer columns, no horizontal overflow and no broken loaded images. The old promotion is absent. Audit evidence is saved as `live-route-audit.json` and `live-responsive-audit.json` in the review visualization directory.

The browser exposed an SSR hydration warning on the homepage. Comparing server markup with the hydrated DOM identified a leftover literal `application/ld+json` script from the static source: Vue's browser compiler omits that template node, whereas SSR included it. Removed that redundant node; the shared shell already emits the dynamic Resort JSON-LD and the carousel/gallery behavior already uses Vue lifecycle handlers. Also corrected phone hint/error references to use each form's unique IDs.

Final validation and deployment of this small follow-up are recorded through its PR. Physical-device swipe acceptance and activation of website submissions remain outstanding; no guest enquiry or external message was sent during verification.

Follow-up local validation: frontend format/lint, Vue TypeScript and client/SSR production builds passed. This removes inert template markup and fixes accessibility references; persistence and notification behavior are unchanged. The required CI suite runs again on the follow-up PR and its merge commit.
