# Jawharat Bidiyah Resort website implementation plan

Status: MVP deployed for protected review at `https://preview.jauharat.com` on 21 September 2026. Management selected DigitalOcean to consolidate hosting with Tamkeen’s other applications, replacing the earlier Hetzner choice. Management also supplied room categories, capacities, staff identities and a 90-day retention decision. Website content remains editable through Filament and may be completed by staff during review. Final review is 26 September 2026; public launch follows sign-off and technical checks. The existing website and Google Workspace MX/SPF records remain unchanged; the preview subdomain and Google DKIM TXT record were added at Wix. Google SMTP relay authorization, external SPF/DKIM checks and staff Inbox delivery are verified. Automatic website notifications are enabled. Public submissions and indexing remain disabled pending content completion and UAT. The earlier staff spam concern was an incorrect inference from an intermediate delivery event; expanded post-delivery details confirm Inbox placement, including for a fresh test.

This document records the approved plan, its implementation, and the remaining launch decisions. Business facts are separated from draft content and proposals throughout.

## 1. Business requirements and evidence

### Confirmed scope

- Public name: **Jawharat Bidiyah Resort**.
- Overnight accommodation is the launch offering.
- English and Arabic are required for launch.
- Visitors can send booking requests and general enquiries. Both collect name, email and contact number.
- Staff confirm reservations manually while operations are being established.
- A website submission does not reserve inventory, guarantee availability, or constitute a confirmed booking.

The primary action is **Request a booking**, with **Make an enquiry** as the secondary action. No payment, live availability, guest account or automated reservation confirmation is included.

### Verified project foundation

The existing Laravel 13 / PHP 8.5 application uses Inertia 3, Vue 3, Tailwind 4 and Filament 5. Existing form controls, dialogs, routing, authentication, database queues and build tooling have been reused. The starting public page was the Laravel starter page; no previous resort implementation was present. The initial database contained framework/authentication tables only.

`docs/info.md` records telephone `+968 90657840`, email `reservations@jauharat.com`, Instagram `@jawharatResorts` and Facebook “Jawharat Resorts.” The owner approved the documented telephone and email on 19 September 2026; these contact links are now published locally. This owner confirmation is not evidence of an independently completed call or email-delivery test. WhatsApp support is unconfirmed, and social profile URLs remain unspecified.

The original small raster logo is preserved at `public/images/image.png`. The subsequently supplied two-page `JBR Logo (2).pdf` provides sharper JB artwork in green on white and cream on green. The PDF is preserved at `resources/images/resort/JBR Logo (2).pdf`; faithful 1053 × 870 PNG exports of its artwork are used for the website's light/dark logo treatments. Only surrounding page margins were removed; no logo elements were redrawn.

### Photograph update during implementation

The owner added six 1600 × 1200 photographs to the images folder:

| Supplied filename label | Visible content                                            | Interpretation limit                                           |
| ----------------------- | ---------------------------------------------------------- | -------------------------------------------------------------- |
| Luxury 1 BR Villa       | Bedroom with large bed and garden-facing windows           | Name comes from filename; capacity and inclusions are unknown. |
| Majan Villa             | Living room with a pool visible through windows            | No assertion that the pool is private or included.             |
| Mazon Villa             | Bedroom with several beds and pool visible through glazing | Bed count is not an approved occupancy limit.                  |
| Oman Villa              | Indoor pool and seating                                    | Ownership/use arrangements await confirmation.                 |
| Salalah Villa           | Pool interior with swing seat and large windows            | No activity or facility entitlement inferred.                  |
| Standard Room           | Room with two beds                                         | No pricing or occupancy inferred.                              |

Original files are preserved in `resources/images/resort/`, outside the public web root. The importer copies them to private Laravel storage and creates responsive web variants. It creates **unpublished** accommodation and media drafts. Following the owner's 19 September approval, all six existing media drafts and their bilingual captions were published locally. Accommodation entries remain drafts; management supplied capacities on 21 September, while descriptions, inclusions and photo mapping can be completed in Filament before those entries are published. No missing facts were inferred from approval.

Four additional JPEGs were supplied on 19 September. They are imported into the local gallery preview with bilingual descriptive captions. Their filenames do not identify an accommodation category, so they are not automatically attached to a room/villa or used to infer occupancy, included services or private facilities.

| Source file in `resources/images/resort/`    | Dimensions  | Observed content                                                   |
| -------------------------------------------- | ----------- | ------------------------------------------------------------------ |
| `WhatsApp Image 2026-09-18 at 23.50.54.jpeg` | 1600 × 1066 | Wide view of a twin-bed room with television and wooden furniture. |
| `WhatsApp Image 2026-09-18 at 23.52.42.jpeg` | 1600 × 1200 | Two beds, bedside table and curtains.                              |
| `WhatsApp Image 2026-09-18 at 23.54.59.jpeg` | 1200 × 1600 | Bathroom washbasin, mirror, towels and reflected shower.           |
| `WhatsApp Image 2026-09-18 at 23.56.40.jpeg` | 1146 × 1600 | Bedroom doorway opening towards a paved courtyard and lawn.        |

The importer now handles ten photographs in total without creating accommodation categories for the four gallery additions. Re-running it preserves published revisions, staff edits and the existing resort profile. The new photographs remain gallery drafts while their category mapping is clarified; the earlier six approvals are preserved.

An official North Sharqiyah Governorate directory previously identified the resort in Bidiyah. Other public listings had conflicting details. These are research leads, not approved accommodation classifications, contact details or claims.

### Audience and operating decisions

The website supports people comparing stays, visitors ready to request dates, and guests needing practical assistance. Domestic, regional and international audience priorities remain assumptions for the owner to validate.

Management named Abdulla Al Noman (`reservations@jauharat.com`) and Abdulla Al Hajri (`admin@jauharat.com`) as reservations/administration contacts. Their coverage arrangement, authoritative availability record, monitored hours and process for quotations, payment arrangements and final confirmations still need operational setup. The website inbox records incoming requests and outcomes; it is not the availability record.

Initial success measures: request volumes, requests awaiting response, time to first staff follow-up, and staff-recorded booking outcomes. No conversion target is assumed before a baseline exists.

## 2. Sitemap and content structure

All pages have English and Arabic equivalents. `/` redirects to `/en`. Language switching retains the corresponding page and selected accommodation on the booking form.

| Page                 | Route after locale      | Content                                                                                        |
| -------------------- | ----------------------- | ---------------------------------------------------------------------------------------------- |
| Home                 | `/`                     | Approved introduction, supplied hero photograph, accommodation preview and booking invitation. |
| Accommodation        | `/accommodation`        | Published categories, photographs and approved details.                                        |
| Accommodation detail | `/accommodation/{slug}` | Photos, description, capacity, inclusions, applicable policies and preselected request link.   |
| Gallery              | `/gallery`              | Approved photographs, bilingual captions and accessible enlargement.                           |
| Plan your stay       | `/plan-your-stay`       | Approved address, directions, arrival information, policies and FAQs.                          |
| Contact              | `/contact`              | Verified channels, response hours and general enquiry form.                                    |
| Request a booking    | `/request-a-booking`    | Preference, dates, guest count and contact details.                                            |
| Privacy              | `/privacy`              | Owner-approved handling and retention notice.                                                  |
| Request received     | `/request-received`     | Session-bound reference and pending-review explanation; private and noindex.                   |

Navigation: Accommodation, Gallery, Plan your stay, Contact, language switch and Request a booking. Empty accommodation, gallery and privacy sections remain unavailable until content exists. Facilities belong within approved accommodation descriptions initially. No unsupported events, dining, day visits, experiences, promotions, ratings or review pages are included.

## 3. Design direction

The implementation uses the supplied logo, real photographs and generous reading space. Teal `#276A70` anchors calls to action; dark green `#173E35` anchors typography and large surfaces; olive-gold `#9B895B` is a restrained decorative accent. These are implementation tokens, not claimed official brand specifications. White reading surfaces and corresponding dark-mode colours follow the existing appearance system.

El Messiri headings pair with Noto Sans and Noto Sans Arabic body text. Fonts are downloaded during the build and served locally, with explicit Arabic subsets. No new font package or runtime font-service dependency was added. The existing Instrument Sans remains for starter account screens. Final Arabic copy and font legibility need a competent reviewer.

The homepage pairs an introduction with a substantial real photograph; accommodation uses consistently labelled image-led listings. Mobile content follows a single reading order. Navigation collapses into a labelled menu; CTAs remain available without covering forms. The gallery uses an accessible dialog with keyboard dismissal and a visible translated close control. Directional spacing and arrows mirror in RTL. Motion is brief and reduced-motion preferences are respected.

Trust comes from accurate photographs, confirmed inclusions, explicit policies, reachable staff and honest confirmation wording. Unsupported luxury claims, fabricated reviews, scarcity counters and invented prices are excluded.

## 4. Visitor and staff journeys

| Situation                  | Behaviour                                                                                                                                           |
| -------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| Discovery → request        | Browse photographs/details, choose a preference or “Help me choose,” enter dates and guest count, then name, email and international phone number.  |
| General enquiry            | Name, email, phone and message; no accommodation or dates required.                                                                                 |
| Successful submission      | Persist first, then queue staff notification and guest receipt. Display a reference and explicitly state that the reservation is not yet confirmed. |
| Staff review               | Staff assigns a responsible person, checks the external availability record, contacts the guest, and records notes and a follow-up date.            |
| Unavailable dates          | Staff offers alternatives when available and records an unavailable or other appropriate outcome. No simulated availability calendar.               |
| Final reservation          | Staff follows the agreed operating process and records the issued reservation reference before marking a booking confirmed.                         |
| Validation error           | Retain entries, display translated field errors and focus an error summary.                                                                         |
| Connection/storage failure | Preserve entries; explain retry options; do not claim success before storage.                                                                       |
| Notification failure       | Keep the request in the inbox, show delivery state and support retries.                                                                             |
| Repeated submission        | Disable the control while sending and deduplicate retries using a session-bound encrypted token and unique database key.                            |
| Expired form               | Display a translated refresh instruction. Tokens expire after two hours.                                                                            |

Phone values normalize Arabic/Persian digits and common separators while requiring an international country code. Dates use Gregorian values and Oman local time (`Asia/Muscat`). Email, telephone and references retain LTR readability within Arabic pages.

Both languages cover navigation, forms, errors, receipts and essential content. Publishing requires both translations and an Arabic-review acknowledgement. Draft previews do not imply translation approval.

## 5. MVP and administration

The MVP includes bilingual public pages, accommodation and gallery, both request flows, a staff inbox, structured content editing, email jobs, publication controls, SEO, accessibility foundations and operational readiness checks.

Filament provides:

- Website content (resort profile): introduction, contacts, directions/map URL, guest and reservation policies (including check-in/out, payment and cancellation), privacy, response hours, SEO description and launch approvals.
- Website text: grouped bilingual editors for the resort name, page headings and introductions, navigation, calls to action, form labels/consent and footer. Existing approved wording is the default for older profiles; saved edits live in the profile draft/published snapshots.
- Brand artwork: staff can upload through Media Assets and select light/dark logos. Selected logos do not become gallery or fallback hero photographs.
- Accommodations: slug, translated content, occupancy, inclusions, photos, ordering and approvals.
- Media: private original uploads, processing status, bilingual captions/alt text, focal point, ordering and rights/property approvals.
- FAQs: bilingual question/answer, ordering and approval.
- Guest inbox: responsible staff, follow-up state/date, notes, outcome, confirmation reference and notification status.

Missing accommodation descriptions, inclusions, photo mapping, directions and policies do not block CMS development or hosting preparation. Staff can save unfinished drafts and complete these fields in the dashboard. Incomplete accommodation entries remain unpublished; empty optional practical-information sections are hidden. Applicable booking terms must be settled before staff confirms reservations.

Saving a draft preserves its published snapshot. Staff saves, previews, then explicitly publishes or unpublishes. No general page builder is introduced. Page structure and technical validation, preview/error messages and booking-status safeguards remain application-controlled; staff manages the website business content without code changes. New media must finish processing before publication. Publication validates required details and reviews.

The native Filament login requires authenticator MFA with recovery codes. Staff uses a separate `resort` session guard, so starter Fortify authentication cannot bypass the panel's MFA login. Public registration is disabled. CLI provisioning deliberately grants/revokes staff access; no default administrator password or production staff account has been created.

Later enhancements require evidence of need: promotions with expiry/owner, approved analytics, live inventory/booking engine, payment integration, additional offerings, newsletters or loyalty. No paid service or new application dependency was added.

## 6. Technical architecture

### Application interfaces

Laravel controllers and Form Requests serve Inertia pages under `resources/js/pages/public`; the public page has an explicit layout mapping. Components reuse existing buttons, inputs, labels and dialogs. Named routes and generated Wayfinder functions connect frontend navigation and submissions.

- `POST /{locale}/booking-requests`
- `POST /{locale}/enquiries`
- `GET /{locale}/request-received`
- Signed staff content previews under `/{locale}/preview/{type}/{record}`
- Versioned image responses under `/website-media/{asset}/{size}/{version}`
- `/admin` for staff
- `/sitemap.xml` for approved indexable pages

There is no separate REST API, payment service or inventory schema.

### Models and publication

`ResortProfile`, `Accommodation`, `MediaAsset` and `Faq` hold editable draft JSON and an independently published snapshot. `GuestRequest` stores visitor input, locale/reference, optional accommodation label snapshot, assignment, follow-up, outcomes and notification status. Staff-access and encrypted MFA fields extend `User`.

The local development database remains SQLite. Production database and storage persistence must be selected with the host. Additive migrations preserve existing framework records. Seeder data is unapproved; factories contain clearly isolated test fixtures only.

### Security and data handling

Session CSRF protection remains in place. Request routes have per-session and per-IP throttles, a honeypot, server validation and expiring session-bound tokens. Public input cannot set assignment, request status or outcome. Required email/phone fields are validated on the server. Stored text is escaped; no public file attachments or arbitrary HTML are accepted.

The database inbox is authoritative. Queue retries and staff retry actions recover delivery failures without losing submissions. Logs use request identifiers rather than guest details. Guest receipts do not expose another guest's information; receipt URLs contain no personal data.

The retention command anonymizes closed requests only after the owner-approved period in the published privacy configuration. It retains non-identifying outcome statistics. Unclosed requests require staff review; backups and logs need their own approved retention schedule. Notification recovery scans recent unsent requests; older failures remain visible for staff review.

Image uploads accept JPEG, PNG and WebP up to 12 MB and 40 megapixels. GD generates responsive 480/960/1600 variants without upscaling; re-encoding strips embedded metadata. Originals and generated files use private Laravel storage. Public responses expose only a published variant; draft responses require a signature plus staff session or local-only preview. Replacing an original preserves its published variant until re-publication. Keep the persistent private storage directory in backups.

### SEO, accessibility and performance

Inertia SSR is configured for production builds with a supervised SSR process. Pages supply translated titles/descriptions, canonical/alternate language links, social metadata and approved `LodgingBusiness` JSON-LD. Unapproved profiles, previews and receipts are noindex. Structured data contains no unverified ratings, prices or facilities. Enable indexing only after content and release checks.

Accessibility targets WCAG 2.2 AA: semantic headings, skip link, labels, visible focus, keyboard interaction, accessible errors, readable contrast, logical RTL layout and reduced motion. Complete screen-reader and real-device review before claiming conformance.

Responsive images include dimensions and size hints; the hero loads eagerly and lower images lazily. Fonts are self-hosted. Target mobile LCP ≤2.5 seconds, CLS ≤0.1 and INP ≤200 ms; field measurements require actual traffic. These targets are not certified by a successful build or desktop preview.

MVP reporting uses the staff inbox and dashboard counts/response times. Third-party traffic analytics requires an owner/provider/privacy decision. A form submission or WhatsApp click is not a confirmed booking.

## 7. Content readiness and decisions

| Input                 | Available now                                                                                                | Remaining launch gate                                                                                                                  |
| --------------------- | ------------------------------------------------------------------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------- |
| English name          | Owner-confirmed                                                                                              | None.                                                                                                                                  |
| Arabic name and copy  | Existing copy accepted by owner on 19 September                                                              | Review any newly supplied translations; no independent language review is claimed.                                                     |
| Logo                  | Two-page PDF master supplied; light/dark artwork used on site                                                | None for the supplied artwork.                                                                                                         |
| Photography           | Six approved photos plus four new gallery preview drafts                                                     | Confirm the new photos' accommodation mapping. Exterior/entrance photos remain desirable; included facilities need staff input.        |
| Catalogue             | Six management-supplied categories and capacities (21 September)                                             | Enter approved details and translations in Filament; complete descriptions, inclusions and photo mapping before publishing each entry. |
| Introduction          | Existing bilingual welcome approved and published locally                                                    | None for the current text.                                                                                                             |
| Address/directions    | General Bidiyah association                                                                                  | Exact address, entrance pin and arrival instructions.                                                                                  |
| Contact channels      | Documented phone/email confirmed by owner and published                                                      | External receipt and staff Inbox delivery verified; complete staff-led form/UAT verification. WhatsApp remains optional and hidden.    |
| Availability workflow | Manual model agreed; two staff contacts supplied                                                             | Provision access and confirm coverage, availability record, quotes/payment/confirmation process.                                       |
| Policies              | No written policy content supplied                                                                           | Supply applicable guest, arrival, booking and cancellation rules.                                                                      |
| Rates                 | None approved                                                                                                | Manual quoting permits request-based launch; publish no guessed rates.                                                                 |
| Privacy and retention | Management selected 90 days after closure                                                                    | Configure the approved period and complete/publish the bilingual privacy notice before collecting guest data.                          |
| Hosting/domain/email  | Protected DigitalOcean review deployed; domain/DNS stay at Wix; SMTP, DKIM and staff Inbox delivery verified | Complete staff UAT before public-domain cutover.                                                                                       |
| Reviews/promotions    | None approved                                                                                                | Omit; not launch blockers.                                                                                                             |

Content completion and infrastructure preparation proceed in parallel. Management has supplied the provider, catalogue names/capacities, staff contacts and retention period; the remaining descriptions, photo mapping, location and policies can be entered dynamically by staff. Account access, an authorized email transport and verified delivery, operating workflow and content publication are checked before launch. Previous approvals remain accepted.

### Management decisions and owner clarifications — 21 September 2026

Sources: management's three-page `Reply as per the serials number.pdf`, the supplied WhatsApp screenshot, the owner's follow-up annotations and the subsequent instruction to use DigitalOcean. These are business decisions, not evidence that infrastructure or mail delivery has been configured.

| Item                  | Decision and implementation consequence                                                                                                                                                                                                                                                                                                                                                                                                      |
| --------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Hosting               | DigitalOcean replaces Hetzner. The owner approved a separate $24/month server in Frankfurt (2 vCPU, 4 GB RAM, 80 GB SSD) and direct administration instead of Ploi. The server is provisioned in a dedicated resort project; no paid add-ons were selected.                                                                                                                                                                                  |
| Domain                | Registration and DNS remain at Wix. Connect external hosting through website DNS records while preserving Google Workspace records.                                                                                                                                                                                                                                                                                                          |
| Retention             | 90 days after a request/enquiry is closed. Configure through Website content and describe it in the bilingual privacy notice.                                                                                                                                                                                                                                                                                                                |
| Staff                 | Abdulla Al Noman: `reservations@jauharat.com`; Abdulla Al Hajri: `admin@jauharat.com`. Provision staff access deliberately; no password is inferred or created here.                                                                                                                                                                                                                                                                         |
| Workspace and SMTP    | `gm@jauharat.com` remains the management-designated Workspace/routing contact. The owner supplied an authenticated Workspace administrator session and approved the IP-authorized relay. Website sender and staff notification recipient are `reservations@jauharat.com`; no mailbox password is stored on the server. Actual TLS delivery, external SPF/DKIM and staff Inbox placement passed; automatic website notifications are enabled. |
| Review and launch     | Final review on 26 September 2026, with public launch after sign-off. The 21 September date is not a claim of a completed release.                                                                                                                                                                                                                                                                                                           |
| Content ownership     | All website business content is editable through Filament in English/Arabic. Missing content is a staff completion task, not a prerequisite for developing or preparing hosting.                                                                                                                                                                                                                                                             |
| Practical information | Exact map URL, arrival/check-in/out information and payment/cancellation policies are editable fields. The supplied Google search screenshot is location context, not an entrance pin or a written policy.                                                                                                                                                                                                                                   |

### DigitalOcean provisioning and email verification — 21 September 2026

The owner approved the $24/month option and direct server administration. A dedicated project and one Droplet were created in the existing Tamkeen HQ account. Billing began on creation; DigitalOcean displays $0.036/hour with a $24 monthly plan price, before applicable taxes. No paid add-ons were selected.

| Item                 | Provisioned value                                                                                                                                                                                                                               |
| -------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Project              | Jawharat Bidiyah Resort (`785b767d-e537-4d58-b605-28c01c73be93`)                                                                                                                                                                                |
| Droplet              | `jbr-web-01`, ID `602359253`                                                                                                                                                                                                                    |
| Region and plan      | Frankfurt FRA1, Basic Regular `s-2vcpu-4gb`                                                                                                                                                                                                     |
| Capacity             | 2 vCPU, 4 GB RAM, 80 GB SSD, 4 TB included transfer                                                                                                                                                                                             |
| OS                   | Ubuntu 24.04 LTS                                                                                                                                                                                                                                |
| Public IPv4          | `134.122.81.15`                                                                                                                                                                                                                                 |
| Administration       | `jbr-admin` through SSH key authentication; direct root and password SSH disabled                                                                                                                                                               |
| Local SSH key        | `~/.ssh/jbr_digitalocean_ed25519`; private key stays on the operator’s computer, outside the repository                                                                                                                                         |
| Runtime              | Nginx, PHP 8.5.10/FPM with required extensions and GD/WebP, Node 22.23.2, Composer 2.10.3, Supervisor and Certbot                                                                                                                               |
| System configuration | UFW denies incoming traffic except rate-limited SSH and HTTP/HTTPS; automatic Ubuntu security updates and free DigitalOcean metrics enabled; 2 GB swap on the existing disk                                                                     |
| Application state    | Protected review deployed at `https://preview.jauharat.com`; HTTPS and review password required; public submissions and indexing disabled. SMTP notifications are enabled after verified delivery tests. Bare-IP HTTP remains a setup response. |

The non-development requirements in the actual Composer lock file passed on the new server. GD/WebP support, SQLite/MySQL PDO drivers, Nginx/PHP-FPM configuration and a fresh non-root administrator SSH connection were verified. After the system-update reboot, Nginx, PHP-FPM, Supervisor, unattended upgrades and the DigitalOcean metrics agent were active; no failed system units or pending reboot were reported. A fresh SSH connection, UFW rules, disabled root/password authentication, swap persistence, external HTTP 503/noindex response and Composer platform requirements all passed again. The subsequent review deployment added HTTPS, the persistent application database, supervised queue/SSR processes and scheduled tasks as recorded below.

DigitalOcean’s published guidance says SMTP ports 25, 465 and 587 are blocked. However, direct tests from **this Droplet** on 21 September succeeded against both `smtp.gmail.com:587` and `smtp-relay.gmail.com:587`: certificate validation passed, TLS 1.3 was negotiated and post-TLS EHLO returned 250. These are observed connectivity results, not evidence of a policy exemption or successful message delivery. [DigitalOcean SMTP guidance](https://docs.digitalocean.com/support/why-is-smtp-blocked/).

The owner's preferred Google Workspace SMTP approach has subsequently been authorized and tested, as recorded below. `gm@jauharat.com` remains the Workspace administrator and routing contact. No credentials were requested in chat and no test email was sent during the initial provisioning stage. Gmail API remains a fallback only if SMTP becomes unavailable; no API integration is approved or implemented.

Management supplied these catalogue facts. They are recorded here without assuming descriptions, facilities, child allowances, bedding arrangements beyond the source, prices or photo assignments. Database catalogue drafts are not automatically published by this documentation update.

| Category                 | Supplied occupancy                        |
| ------------------------ | ----------------------------------------- |
| Standard Room            | 1 or 2 guests (single or double/twin)     |
| Luxury One Bedroom Villa | 1 or 2 guests (single or double/twin)     |
| Oman Villa               | 2 guests; source labels occupancy DBL/SGL |
| Mazon Villa              | Maximum 6 adults                          |
| Majan Villa              | Maximum 6 adults                          |
| Salalah Villa            | Maximum 8 adults                          |

### Owner approval applied — 19 September 2026

The owner replied “approved” to the pending content, policies, contact and staff-UAT gate. This is recorded as owner acceptance of the current work and authorization to proceed, not as a claim that unobserved technical tests took place.

- Published the existing profile and all six processed photograph records in the local database, including the current English/Arabic text and the documented phone/email links.
- Recorded the owner's acceptance of the manual staff-confirmation process. A named staff login, availability record and real email delivery are still operational inputs to supply.
- Preserved pre-approval profile/media values in private local storage under `website/approval-backups/` before updating them.
- Kept accommodation drafts unpublished: approval cannot supply missing capacities, descriptions or inclusions.
- Privacy text and a retention period are absent. The owner has been asked to choose the retention period so the bilingual notice can describe the implemented handling accurately.
- The existing mail transport points to a local test server, and no staff account exists. The email service/configuration location and staff email have been requested. No credentials, guest messages or external deployment were created as part of applying this approval.
- Live-request and indexing switches remain off while these concrete configuration gaps are resolved. The local preview continues to expose draft accommodation for review.

## 8. Delivery roadmap and prioritised backlog

| Priority | Phase                              | Deliverables and acceptance                                                                                                                                                                      |
| -------- | ---------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| P0       | Operating/content brief            | Approve the items above; staff can explain how a request becomes a reservation; each public claim has an approved source.                                                                        |
| P1       | Foundation and first complete flow | Bilingual routes/layout, brand tokens, protected admin and persisted booking-request handoff. Implemented locally.                                                                               |
| P1       | Public site and structured CMS     | Catalogue, gallery, practical information, enquiry flow, bilingual snapshots and mail templates. Implemented; missing catalogue/policy details remain to be supplied.                            |
| P1       | Automated QA and browser review    | Validation, draft/privacy/access boundaries, duplicates, queues/failure modes, responsive/RTL checks and production builds. Record actual evidence below.                                        |
| P1       | Staff UAT                          | Owner/reviewer uses both languages, edits drafts, publishes, handles available/unavailable requests, retries email and records a confirmed booking reference. Requires real staff participation. |
| P1       | Deployment preparation             | Approve host/domain; verify email, private persistence, SSL, queue/SSR supervision, scheduler and offsite backups. Rehearse restore and rollback in protected staging.                           |
| P1       | Release and verification           | Later authorized deployment; verify bilingual pages, metadata, indexing, media, submission receipt and staff visibility after release. Repeat after 24–48 hours and first operating week.        |
| P2       | Evidence-led additions             | Analytics, promotions, live booking/payment integration or other confirmed offerings, each with an operating owner and approved cost.                                                            |

The recommended first implementation phase was the bilingual foundation plus one complete staff-confirmed booking-request flow. That foundation is now present and the owner has approved the current implementation. The protected DigitalOcean review deployment and SMTP notifications are available. An external receipt reached the owner's Gmail Inbox with SPF/DKIM passing, and the staff notification's final location is Inbox. Next, staff enrols MFA and completes content through Filament. Complete the visitor-to-staff UAT and review the release on 26 September before public launch.

### Local operation

Run from the project root:

```sh
php artisan migrate
php artisan website:import-photographs
npm run build:ssr
WEBSITE_LOCAL_PREVIEW=true php artisan serve --host=127.0.0.1 --port=8000
```

The importer is repeatable and preserves existing edits. It imports unapproved drafts; it never publishes. `WEBSITE_LOCAL_PREVIEW` works only with `APP_ENV=local` and disables form submissions. Keep the preview bound to loopback.

In separate terminals, use `php artisan inertia:start-ssr` for rendered HTML and `php artisan queue:work --timeout=180` for queued image/mail work. Queue connection retry-after must exceed the worker timeout (see release preparation). A local preview does not require actual mail delivery.

Provision staff deliberately with `php artisan website:staff staff@example.com --name="Staff name"` using the actual approved identity. New accounts prompt for a password; no password is accepted in command-line arguments. The administrator completes authenticator enrolment at `/admin`. `--revoke` removes panel access.

`php artisan website:readiness` reports outstanding gates. `php artisan website:maintain-requests` applies approved retention and recovers recent notification failures; it is scheduled hourly through Laravel's scheduler.

### Public launch preparation (after protected review deployment)

1. Approve the production database, host, HTTPS/domain and persistent private storage; back up before migrations. Set `APP_ENV=production`, `APP_DEBUG=false`, correct `APP_URL`, secure session cookies and trusted proxy/host configuration appropriate to the host.
2. Retain the verified Google relay transport and `reservations@jauharat.com` sender/staff recipient. Real port 587 TLS delivery and queued English/Arabic notification processing passed. The owner-approved external receipt reached Gmail's Inbox with SPF/DKIM passing; staff post-delivery details also confirm Inbox placement. `WEBSITE_MAIL_ENABLED=true` is configured. No DMARC policy is published. Review DMARC and exercise failure recovery during staff UAT. An API transport is only a fallback and requires separate approval and implementation.
3. Keep `WEBSITE_LOCAL_PREVIEW=false`. Publish reviewed photos, catalogue, FAQs and profile; approve operational and privacy fields. Set `WEBSITE_ACCEPT_REQUESTS=true` after UAT. Production acceptance also requires the published profile's request/approval flags.
4. Build client and SSR assets. Supervise SSR and queue processes, use queue retry-after greater than worker timeout, restart workers on releases, and run the scheduler every minute. Set `DB_QUEUE_RETRY_AFTER=240` when using the documented 180-second worker timeout.
5. Back up both the database and `storage/app/private`; confirm restore, access restrictions, retention and the rollback release. Do not use a public storage link for original resort images.
6. Protect staging against indexing and uninvited access. Enable `WEBSITE_INDEXABLE=true` only after approval, then check canonical/alternate URLs, sitemap, metadata and rendered HTML on the final domain.
7. Submit a labelled agreed test request once mail routing is verified, verify both receipt and staff inbox, exercise an unavailable-date response, and inspect errors/worker health. Obtain staff sign-off. The protected review deployment is complete; no real guest messages have been sent.

### Verification checklist

- Automated: locale routes; draft/public snapshots; missing pages; private receipts; signed previews; both forms; email/phone/dates; input type attacks; duplicate retry; throttle; notification failure/retry; retention; published image boundaries; processing failure; panel access/MFA isolation; disabled public registration; staff content and inbox actions.
- Browser: English/Arabic desktop and phones at 360/390 px, tablet, landscape, gallery close/focus, selected accommodation retention, keyboard operation, 200% zoom and reduced motion. Use Safari and Chromium on actual devices before launch.
- Administrator UAT: content edit/save/preview/publish/unpublish; photo replacement/processing failure; request assignment and notes; available/unavailable dates; final reference; failed-email retry; recovery access and staff backup.
- Release: client/SSR build, repository CI, real email delivery, backup restore, deployment rollback, private media, production no-debug, scheduler/queue/SSR supervision, indexing and post-launch checks.

Local verification results are recorded below after execution; external UAT, actual mobile Safari/screen-reader verification, email deliverability, restore rehearsal and production acceptance remain separate gates.

### Local verification results

- `composer ci:check` passed: frontend formatting/lint, Vue TypeScript, PHP formatting, PHPStan and the existing/expanded test suite. After the final indexing guard was added, affected feature tests and PHPStan passed again.
- Complete suite after the additional asset import: **91 tests, 89 passed, 2 skipped, 554 assertions**. The two registration tests are skipped because public registration is disabled; separate tests verify both registration endpoints are unavailable.
- Production client and SSR builds passed. The installed Inertia plugin emits a sourcemap warning during SSR compilation; this did not prevent the build or rendered output. No dependency was changed to work around that upstream warning.
- The local SSR health check passed; the initial homepage HTTP response includes the rendered heading and noindex metadata before client JavaScript runs.
- Browser review covered the English and Arabic homepages, mobile menu, gallery dialog and Escape/focus return, booking-form layout and selected-accommodation language switching. Reviewed 360/390-pixel phones, 1024-pixel tablet and 1440-pixel desktop in the Chromium-based in-app browser, including light/dark appearance. No horizontal overflow was observed in the inspected layouts; browser console checks returned no warnings/errors.
- All six originals remain preserved. Their generated responsive variants total approximately 1.6 MB, compared with approximately 18 MB of source images.
- Additional asset checks: the four new JPEG originals and two-page logo PDF are preserved in `resources/images/resort/`. All ten photographs have processed variants. Import tests verify gallery-only additions, repeatability and preservation of existing edits/publications; PHPStan, Pint, frontend lint/types and client/SSR builds passed. Browser inspection verified ten gallery items in both languages, the portrait bathroom image in a mobile dialog, translated captions and the light/dark logo variants. The inspected 390-pixel Arabic layout had no horizontal overflow or console errors.
- Draft preview is clearly labelled and form submission is disabled there. The profile and six media records were subsequently published locally on owner approval; incomplete accommodation records remain drafts. `website:readiness` reports the remaining content/configuration gaps.
- Owner acceptance was received on 19 September. Independent evidence remains outstanding for a staff-led end-to-end exercise, qualified Arabic language review, actual Safari/mobile-device and screen-reader review, formal zoom/reflow and performance measurements, real email delivery, backup restoration, production deployment and post-launch verification. These are not presented as completed merely because approval was received.

### Dynamic content verification — 21 September 2026

- Added 88 bilingual website-copy controls and selectable light/dark brand artwork to the existing Filament profile workflow. Existing database snapshots retain their appearance through the original copy defaults; no automatic content publication occurred.
- New tests exercise staff save/publish, incomplete drafts, both locales, signed draft preview, invalid translations/unknown settings and logo publication/gallery separation. The 12 new cases passed with 133 assertions.
- The 51 affected existing page, publishing, administration, media and request tests passed with 407 assertions. TypeScript and PHPStan passed. Client and SSR builds passed; the existing Inertia sourcemap warning remains.
- Final `composer ci:check` passed: formatting/lint, Vue types, Pint, PHPStan and the complete suite (**103 tests: 101 passed, 2 existing registration skips, 687 assertions**). The final affected page/content tests were run after asset compilation completed.
- Browser verification covered English home/contact and Arabic contact/booking, including mobile navigation at 390 px. The inspected Arabic page had correct `lang`/RTL direction and no horizontal overflow; current browser warning/error logs were empty. Staff edit/publish behaviour was verified through Filament/Livewire feature tests; staff-led UAT remains scheduled for the review milestone.

### Protected review deployment — 21 September 2026

The owner authorized deployment after server provisioning. This is a private review release, not the public launch.

| Item             | Deployed state                                                                                                                                                                                                                                                                                                                        |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Review website   | `https://preview.jauharat.com/en` and `/ar`, protected by a separate HTTP review password. Unauthenticated page and asset requests return 401.                                                                                                                                                                                        |
| DNS              | Added `preview.jauharat.com A 134.122.81.15`, TTL one hour, in Wix. Subsequently added `google._domainkey.jauharat.com TXT` for Google DKIM, also TTL one hour. Existing apex, `www`, language subdomains, MX and other TXT records were retained.                                                                                    |
| HTTPS            | Let's Encrypt certificate installed, initially expiring 20 December 2026. Renewal timer and Nginx reload hook configured; simulated renewal passed.                                                                                                                                                                                   |
| Release          | `/var/www/jbr/releases/20260921-review-01`, selected through `/var/www/jbr/current`. Locked production Composer dependencies and client/SSR assets built on the server.                                                                                                                                                               |
| Persistence      | Fresh production key and environment at `/var/www/jbr/shared/.env`; private SQLite database in WAL mode and persistent storage under `/var/www/jbr/shared`. No local users, sessions, requests, jobs or application credentials were imported.                                                                                        |
| Imported content | One resort profile, ten media records and six accommodation drafts, preserving publication state. Originals and variants are in private Laravel storage; originals are not directly accessible from the web.                                                                                                                          |
| Retention        | Management's 90-day value saved in the deployed profile's draft and published settings. Actual anonymization additionally requires the approved published privacy setting; the bilingual notice still needs completion.                                                                                                               |
| Staff            | Accounts created for `reservations@jauharat.com` and `admin@jauharat.com`. Each staff member must enrol their own authenticator. Unique initial passwords and the separate review login were saved in an owner-only handoff file on the operator's Mac, outside the repository. The server's plaintext provisioning file was removed. |
| Services         | Dedicated `jbr` PHP-FPM pool, Supervisor programs `jbr-queue` and `jbr-ssr`, and a cron entry running the scheduler every minute. SSR binds only to `127.0.0.1:13714`.                                                                                                                                                                |
| Launch switches  | `APP_ENV=production`, debug off, encrypted secure sessions, local preview off. Notification sending is enabled after delivery verification. Guest submissions and search indexing remain disabled. Nginx sends `noindex, nofollow`; review robots.txt disallows crawling.                                                             |
| Email            | Google relay is authorized for this server IP with TLS required. The private environment uses SMTP with a 20-second timeout and no mailbox password. External authentication and staff Inbox placement are verified; automatic website notifications are enabled. See verification details below.                                     |

Verification completed:

- Predeployment `composer ci:check`: frontend formatting/lint and types, Pint, PHPStan, and **103 tests: 101 passed, two existing registration skips, 687 assertions**. The initial sandbox worker-socket restriction was resolved by running the same checks with the required local permission.
- Both production bundles built successfully. Frontend formatting/lint/types passed again after explicitly binding SSR to loopback; the rebuilt SSR service passed its health check.
- HTTPS checks passed for the English/Arabic home, gallery, contact, booking-request and plan-your-stay pages. Initial HTML contains rendered headings and correct language/direction. Both forms report that requests are disabled. Unpublished privacy pages return 404 as intended.
- All 22 page-referenced assets passed HTTP checks after correcting the generated public asset directory's group permissions for Nginx. Private environment/original paths and disabled registration remain inaccessible.
- Queue/SSR processes are running, required services are enabled, the scheduler's minute-by-minute execution is visible in the system journal, the maintenance command runs, and no failed system units were reported.
- Final source artifact: `application-final.tar.gz`, SHA-256 `299ca95a72bc5ee52070ed4275a5116f0491b325110b05e764bc3f6c677c9d74`. It contains the tested working-tree implementation and final Vite SSR setting; it is not represented as a Git release or commit.

Remaining work: have staff sign in, enrol MFA and complete/publish bilingual content; complete protected-site browser/device review and staff UAT. External receipt authentication and both external/staff Inbox placement were verified as recorded below. Browser automation reached the HTTP authentication gate and has not completed an authenticated review of this deployed release. Final production-domain cutover, submission/indexing enablement and public launch follow the review sign-off.

### Google Workspace SMTP verification — 21 September 2026

- The owner approved saving the relay rule. `Jawharat website - DigitalOcean jbr-web-01` (rule `1315e`) is enabled for the root organizational unit. It accepts only `134.122.81.15`, allows senders in Workspace domains and requires TLS. SMTP password authentication is not used. Existing mail-routing rules were not changed.
- Confirmed that the reservations, admin and GM Workspace mailboxes are active. The website sends as `reservations@jauharat.com`; staff notifications go to that address. `admin@jauharat.com` received labelled guest-receipt tests.
- Four initial messages exercised the actual queued notification job for English and Arabic. Google accepted all four, and all staff/guest notification states became `sent`. Two clearly labelled synthetic enquiry records (IDs 1 and 2) were closed with outcome `answered`; exclude them from business reporting. No guest reservation was created.
- Google Email Log Search confirmed TLS reception from the Droplet and mailbox delivery, but marked the first staff notification as spam. Google DKIM was previously inactive. Added a 2048-bit public key at `google._domainkey.jauharat.com` in Wix, verified it at Wix's authoritative nameserver and enabled signing; Google now reports **Authenticating email with DKIM**. Existing SPF remains `v=spf1 include:_spf.google.com ~all`. No DMARC record was found or added.
- A subsequent guest receipt reached `admin@jauharat.com` without a spam event in the displayed delivery log (message `e6579655a425b260761519c1954bfb6a@jauharat.com`). A staff notification reached `reservations@jauharat.com` with an intermediate **Marked spam** event (message `f93315c9d7ed8ea48fbf943f98baf9fe@jauharat.com`). This was initially reported incorrectly as final Spam-folder placement. Expanding the recipient and **Post-delivery message details** later showed **Location: Inbox**, unopened/unread and marked important. Delivery events must be distinguished from the recipient's current mailbox location.
- Saved `MAIL_MAILER=smtp` and `MAIL_URL="smtp://smtp-relay.gmail.com:587?require_tls=true&timeout=20&local_domain=preview.jauharat.com"` in the private server environment. Runtime checks confirmed required TLS and the 20-second timeout. Notifications were initially held disabled during verification and enabled after the final Inbox check below.
- The owner then approved one labelled test to their personal Gmail address. At 13:40 Qatar time, the actual guest-receipt template was sent through the saved server SMTP configuration (message `140812147b9bcc38ce3cd65ec154eaff@jauharat.com`). The recipient's Gmail API showed the message in **INBOX**, with no SPAM label. Received headers confirmed **TLS 1.3**, **SPF pass** for `reservations@jauharat.com` and **DKIM pass** for `jauharat.com`, selector `google`. No DMARC result was reported; this is not claimed as DMARC verification. Exactly one external test was sent, and no new guest request was created.
- After the owner's instruction to proceed, a fresh staff-template test was sent at 13:54 Qatar time (message `358634c53e7d89e9ee54858ac51070d2@jauharat.com`). Its expanded post-delivery details also confirmed **Location: Inbox**, unopened/unread and marked important. The intermediate spam event still appears in the processing trace, but the final mailbox location is Inbox. No filtering, allowlist or routing changes were needed, and no message was manually moved or marked as not spam.
- Enabled `WEBSITE_MAIL_ENABLED=true`, rebuilt the Laravel configuration cache and restarted `jbr-queue`. Final runtime verification confirmed SMTP with required TLS, a 20-second timeout, zero queued jobs and zero failed jobs. Queue and SSR services were running. Public submissions and indexing remain off: `WEBSITE_ACCEPT_REQUESTS=false`, `WEBSITE_INDEXABLE=false`.
- Next: complete staff MFA enrolment, bilingual content publication and staff-led form/UAT verification. No application dependency or mail-template change was made during this infrastructure work. Inbox delivery is verified for these tests; future placement still depends on receiver filtering.

Reference: [Google's DKIM setup and verification guidance](https://knowledge.workspace.google.com/admin/security/set-up-dkim). A saved signing setting alone is not proof that a received message passes DKIM; recipient headers provide that evidence.

For delivery interpretation, [Google distinguishes processing events from post-delivery mailbox status](https://knowledge.workspace.google.com/admin/gmail/advanced/email-log-search-delivery-status-definitions). Inspect both before reporting Inbox or Spam-folder placement.
