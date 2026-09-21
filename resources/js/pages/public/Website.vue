<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowDown,
    ArrowUpRight,
    Check,
    Menu,
    X,
    MapPin,
    ArrowRight,
} from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogDescription,
    DialogClose,
} from '@/components/ui/dialog';
import RequestForm from '@/components/website/RequestForm.vue';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import * as routes from '@/routes/website';
import type {
    Accommodation,
    Locale,
    ResortImage,
    ResortProfile,
    WebsitePage,
} from '@/types/website';
const props = defineProps<{
    locale: Locale;
    copy: Record<string, string>;
    page: WebsitePage;
    profile: ResortProfile;
    hero: ResortImage | null;
    branding: { light: ResortImage | null; dark: ResortImage | null };
    media: ResortImage[];
    accommodations: Accommodation[];
    faqs: { id: number; question: string; answer: string }[];
    stay: Accommodation | null;
    receipt: { reference: string; kind: string } | null;
    isPreview: boolean;
    acceptsRequests: boolean;
    indexable: boolean;
    alternateUrl: string;
    canonicalUrl: string;
    submissionToken: string | null;
    selectedAccommodation: string | null;
    today: string;
}>();
const ar = computed(() => props.locale === 'ar');
const systemText = (en: string, arabic: string): string =>
    ar.value ? arabic : en;
const text = (key: string): string => props.copy[key] ?? '';
const brand = computed(() => text('brand_name'));
const menu = ref(false);
const lightbox = ref<ResortImage | null>(null);
watch(
    () => props.page,
    () => {
        menu.value = false;
    },
);
watch(
    () => props.locale,
    () => {
        menu.value = false;
    },
);
watch(
    () => props.locale,
    (locale) => {
        if (typeof document !== 'undefined') {
            document.documentElement.lang = locale;
            document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
        }
    },
    { immediate: true },
);
const titles = computed<Record<WebsitePage, string>>(() => ({
    home: text('home_title'),
    accommodation: text('accommodation_title'),
    stay: props.stay?.title ?? '',
    gallery: text('gallery_title'),
    plan: text('plan_title'),
    contact: text('contact_title'),
    booking: text('booking_title'),
    privacy: text('privacy_title'),
    received: text('received_title'),
}));
const navigation = computed(() => [
    ...(props.accommodations.length
        ? [
              {
                  label: text('navigation_accommodation'),
                  href: routes.accommodations(props.locale).url,
                  active: ['stay', 'accommodation'].includes(props.page),
              },
          ]
        : []),
    ...(props.media.length
        ? [
              {
                  label: text('navigation_gallery'),
                  href: routes.gallery(props.locale).url,
                  active: props.page === 'gallery',
              },
          ]
        : []),
    {
        label: text('navigation_plan'),
        href: routes.plan(props.locale).url,
        active: props.page === 'plan',
    },
    {
        label: text('navigation_contact'),
        href: routes.contact(props.locale).url,
        active: props.page === 'contact',
    },
]);
const requestLink = (stay?: Accommodation): string =>
    routes.booking(
        props.locale,
        stay ? { query: { accommodation: stay.id } } : undefined,
    ).url;
const description = computed(
    () =>
        props.stay?.description ||
        props.profile.seo_description ||
        props.profile.introduction ||
        brand.value,
);
const structuredData = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'LodgingBusiness',
        name: brand.value,
        url: routes.home(props.locale).url,
        ...(props.profile.phone ? { telephone: props.profile.phone } : {}),
        ...(props.profile.email ? { email: props.profile.email } : {}),
        ...(props.profile.address ? { address: props.profile.address } : {}),
        ...(props.hero ? { image: props.hero.large } : {}),
    }).replace(/</g, '\\u003c'),
);
</script>
<template>
    <div class="resort-site" :dir="ar ? 'rtl' : 'ltr'" :lang="locale">
        <Head :title="page === 'home' ? brand : titles[page] + ' — ' + brand">
            <link
                rel="icon"
                :href="branding.light?.src ?? '/images/jbr-logo-on-white.png'"
                head-key="icon"
            />
            <link
                rel="apple-touch-icon"
                :href="branding.light?.src ?? '/images/jbr-logo-on-white.png'"
                head-key="touch-icon"
            />
            <meta
                name="description"
                :content="description.slice(0, 180)"
                head-key="description"
            />
            <meta
                name="robots"
                :content="
                    indexable && page !== 'received'
                        ? 'index, follow'
                        : 'noindex, nofollow'
                "
                head-key="robots"
            />
            <link rel="canonical" :href="canonicalUrl" head-key="canonical" />
            <link
                v-if="page !== 'received'"
                rel="alternate"
                :hreflang="locale"
                :href="canonicalUrl"
                head-key="language-current"
            />
            <link
                v-if="page !== 'received'"
                rel="alternate"
                :hreflang="ar ? 'en' : 'ar'"
                :href="alternateUrl"
                head-key="language-alternate"
            />
            <meta
                property="og:title"
                :content="brand + ' — ' + titles[page]"
                head-key="og-title"
            />
            <meta
                property="og:description"
                :content="description.slice(0, 180)"
                head-key="og-description"
            />
            <meta property="og:url" :content="canonicalUrl" head-key="og-url" />
            <meta property="og:type" content="website" head-key="og-type" />
            <meta
                property="og:locale"
                :content="ar ? 'ar_OM' : 'en_GB'"
                head-key="og-locale"
            />
            <meta
                v-if="hero"
                property="og:image"
                :content="hero.large"
                head-key="og-image"
            />
            <component
                :is="'script'"
                v-if="indexable && page === 'home'"
                type="application/ld+json"
                v-html="structuredData"
            />
        </Head>
        <a href="#main" class="resort-skip">{{ text('accessibility_skip') }}</a>
        <div v-if="isPreview" class="preview-bar">
            {{
                systemText(
                    'Design preview · Includes draft content · Requests disabled',
                    'معاينة التصميم · تتضمن محتوى أولياً · الطلبات غير مفعّلة',
                )
            }}
        </div>
        <header class="resort-header resort-wrap">
            <Link
                :href="routes.home(locale)"
                class="brand"
                :aria-label="brand + ' — ' + text('accessibility_home')"
                ><img
                    :src="
                        branding.light?.src ?? '/images/jbr-logo-on-white.png'
                    "
                    class="resort-logo-light"
                    :width="branding.light?.width ?? 1053"
                    :height="branding.light?.height ?? 870"
                    :alt="brand" /><img
                    :src="branding.dark?.src ?? '/images/jbr-logo-on-green.png'"
                    class="resort-logo-dark"
                    :width="branding.dark?.width ?? 1053"
                    :height="branding.dark?.height ?? 870"
                    :alt="brand"
            /></Link>
            <nav
                :aria-label="text('accessibility_navigation')"
                class="hidden items-center gap-7 xl:flex"
            >
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    :aria-current="item.active ? 'page' : undefined"
                    class="nav-link"
                    >{{ item.label }}</Link
                >
            </nav>
            <div class="flex items-center gap-3 sm:gap-5">
                <Link
                    :href="alternateUrl"
                    :lang="ar ? 'en' : 'ar'"
                    class="language-link"
                    :aria-label="
                        ar ? 'Switch to English' : 'التبديل إلى العربية'
                    "
                    >{{ ar ? 'EN' : 'العربية' }}</Link
                >
                <Link
                    :href="requestLink()"
                    class="resort-button hidden sm:inline-flex"
                    >{{ text('actions_booking')
                    }}<ArrowUpRight
                        class="size-4 rtl:-scale-x-100"
                        aria-hidden="true"
                /></Link>
                <button
                    class="menu-button xl:hidden"
                    :aria-expanded="menu"
                    aria-controls="mobile-menu"
                    :aria-label="
                        menu
                            ? text('accessibility_close_menu')
                            : text('accessibility_open_menu')
                    "
                    @click="menu = !menu"
                >
                    <X v-if="menu" /><Menu v-else />
                </button>
            </div>
        </header>
        <nav
            v-if="menu"
            id="mobile-menu"
            class="mobile-menu resort-wrap xl:hidden"
            :aria-label="text('accessibility_mobile_navigation')"
            @keydown.esc="menu = false"
        >
            <Link
                v-for="item in navigation"
                :key="item.href"
                :href="item.href"
                @click="menu = false"
                >{{ item.label }}</Link
            ><Link
                :href="requestLink()"
                class="resort-link"
                @click="menu = false"
                >{{ text('actions_booking') }}</Link
            >
        </nav>
        <main id="main" tabindex="-1">
            <template v-if="page === 'home'">
                <section class="home-hero resort-wrap">
                    <div class="hero-copy">
                        <p class="eyebrow">
                            {{ text('home_welcome') }}
                        </p>
                        <h1>{{ titles.home }}</h1>
                        <p class="hero-description">
                            {{ profile.introduction || text('home_preparing') }}
                        </p>
                        <Link
                            :href="
                                accommodations.length
                                    ? routes.accommodations(locale)
                                    : requestLink()
                            "
                            class="resort-button"
                            >{{
                                accommodations.length
                                    ? text('actions_explore_stays')
                                    : text('navigation_plan')
                            }}<ArrowUpRight
                                class="size-5 rtl:-scale-x-100"
                                aria-hidden="true" /></Link
                        ><a
                            v-if="accommodations.length"
                            href="#stays"
                            class="hero-scroll"
                            ><ArrowDown class="size-4" aria-hidden="true" />{{
                                text('home_scroll')
                            }}</a
                        >
                    </div>
                    <figure v-if="hero" class="hero-photo">
                        <ResortPhoto :photo="hero" eager />
                        <figcaption>
                            <span>{{ hero.caption }}</span
                            ><Link :href="routes.gallery(locale)"
                                >{{ text('actions_gallery')
                                }}<ArrowUpRight
                                    class="size-4 rtl:-scale-x-100"
                                    aria-hidden="true"
                            /></Link>
                        </figcaption>
                    </figure>
                    <div v-else class="brand-panel" aria-hidden="true">
                        <img
                            :src="
                                branding.light?.src ??
                                '/images/jbr-logo-on-white.png'
                            "
                            class="resort-logo-light"
                            alt=""
                            :width="branding.light?.width ?? 1053"
                            :height="branding.light?.height ?? 870"
                        />
                        <img
                            :src="
                                branding.dark?.src ??
                                '/images/jbr-logo-on-green.png'
                            "
                            class="resort-logo-dark"
                            alt=""
                            :width="branding.dark?.width ?? 1053"
                            :height="branding.dark?.height ?? 870"
                        />
                    </div>
                </section>
                <section
                    v-if="accommodations.length"
                    id="stays"
                    class="resort-wrap section-space"
                >
                    <div class="section-heading">
                        <div>
                            <p class="eyebrow">
                                {{ text('home_accommodation_label') }}
                            </p>
                            <h2>
                                {{ text('home_accommodation_heading') }}
                            </h2>
                        </div>
                        <Link
                            :href="routes.accommodations(locale)"
                            class="resort-link"
                            >{{ text('actions_all_stays')
                            }}<ArrowRight
                                class="size-4 rtl:-scale-x-100"
                                aria-hidden="true"
                        /></Link>
                    </div>
                    <div class="stay-grid">
                        <article
                            v-for="(item, index) in accommodations.slice(0, 3)"
                            :key="item.id"
                            class="stay-card"
                        >
                            <Link
                                :href="
                                    routes.accommodation({
                                        locale,
                                        slug: item.slug,
                                    })
                                "
                                class="stay-image"
                                ><ResortPhoto
                                    v-if="item.images[0]"
                                    :photo="item.images[0]"
                                    sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                                /><span
                                    class="photo-number"
                                    aria-hidden="true"
                                    >{{
                                        String(index + 1).padStart(2, '0')
                                    }}</span
                                ></Link
                            >
                            <div class="stay-heading">
                                <h3>
                                    <Link
                                        :href="
                                            routes.accommodation({
                                                locale,
                                                slug: item.slug,
                                            })
                                        "
                                        >{{ item.title }}</Link
                                    >
                                </h3>
                                <ArrowUpRight
                                    class="size-5 rtl:-scale-x-100"
                                    aria-hidden="true"
                                />
                            </div>
                            <p v-if="item.capacity" class="resort-muted">
                                {{ text('accommodation_capacity_prefix') }}
                                {{ item.capacity }}
                                {{ text('accommodation_capacity_suffix') }}
                            </p>
                        </article>
                    </div>
                </section>
                <section class="resort-wrap">
                    <div class="stay-invitation">
                        <div>
                            <p class="eyebrow">
                                {{ text('home_invitation_label') }}
                            </p>
                            <h2>
                                {{ text('home_invitation_heading') }}
                            </h2>
                            <p>
                                {{ text('home_invitation_description') }}
                            </p>
                        </div>
                        <Link
                            :href="requestLink()"
                            class="resort-button inverse"
                            >{{ text('actions_booking')
                            }}<ArrowUpRight
                                class="size-5 rtl:-scale-x-100"
                                aria-hidden="true"
                        /></Link>
                    </div>
                </section>
            </template>
            <template v-else>
                <section class="page-heading resort-wrap">
                    <p class="eyebrow">{{ brand }}</p>
                    <h1>{{ titles[page] }}</h1>
                    <p v-if="page === 'accommodation'">
                        {{ text('accommodation_description') }}
                    </p>
                    <p v-if="page === 'gallery'">
                        {{ text('gallery_description') }}
                    </p>
                    <p v-if="page === 'booking' || page === 'contact'">
                        {{ text('requests_description') }}
                    </p>
                </section>
                <section
                    v-if="page === 'accommodation'"
                    class="resort-wrap listing-grid"
                >
                    <article
                        v-for="item in accommodations"
                        :key="item.id"
                        class="stay-card"
                    >
                        <Link
                            :href="
                                routes.accommodation({
                                    locale,
                                    slug: item.slug,
                                })
                            "
                            class="stay-image"
                            ><ResortPhoto
                                v-if="item.images[0]"
                                :photo="item.images[0]"
                        /></Link>
                        <div class="stay-heading">
                            <h2>
                                <Link
                                    :href="
                                        routes.accommodation({
                                            locale,
                                            slug: item.slug,
                                        })
                                    "
                                    >{{ item.title }}</Link
                                >
                            </h2>
                            <ArrowUpRight
                                class="size-5 rtl:-scale-x-100"
                                aria-hidden="true"
                            />
                        </div>
                        <p
                            v-if="item.description"
                            class="resort-muted line-clamp-3"
                        >
                            {{ item.description }}
                        </p>
                        <p v-if="item.capacity" class="mt-3 text-sm">
                            {{ text('accommodation_capacity_prefix') }}
                            {{ item.capacity }}
                            {{ text('accommodation_capacity_suffix') }}
                        </p>
                        <Link
                            :href="requestLink(item)"
                            class="resort-link mt-4"
                            >{{ text('actions_request_stay') }}</Link
                        >
                    </article>
                </section>
                <section v-if="page === 'stay' && stay" class="resort-wrap">
                    <div class="stay-detail">
                        <div>
                            <button
                                v-if="stay.images[0]"
                                class="detail-photo"
                                :aria-label="text('accessibility_enlarge')"
                                @click="lightbox = stay.images[0]"
                            >
                                <ResortPhoto :photo="stay.images[0]" eager />
                            </button>
                            <p
                                v-if="stay.description"
                                class="body-copy mt-8 whitespace-pre-line"
                            >
                                {{ stay.description }}
                            </p>
                            <div v-if="stay.inclusions" class="content-block">
                                <h2>
                                    {{ text('accommodation_inclusions') }}
                                </h2>
                                <p class="whitespace-pre-line">
                                    {{ stay.inclusions }}
                                </p>
                            </div>
                            <p
                                v-if="isPreview && !stay.description"
                                class="resort-notice mt-6"
                            >
                                {{
                                    systemText(
                                        'Accommodation details await resort approval.',
                                        'تفاصيل الإقامة بانتظار اعتماد المنتجع.',
                                    )
                                }}
                            </p>
                        </div>
                        <aside class="booking-aside">
                            <p class="eyebrow">
                                {{ text('accommodation_booking_label') }}
                            </p>
                            <h2>{{ stay.title }}</h2>
                            <p v-if="stay.capacity">
                                {{ text('accommodation_capacity_prefix') }}
                                {{ stay.capacity }}
                                {{ text('accommodation_capacity_suffix') }}
                            </p>
                            <p>
                                {{ text('accommodation_booking_description') }}
                            </p>
                            <Link
                                :href="requestLink(stay)"
                                class="resort-button"
                                >{{ text('actions_booking') }}</Link
                            ><Link
                                :href="routes.contact(locale)"
                                class="resort-link"
                                >{{ text('actions_question') }}</Link
                            >
                        </aside>
                    </div>
                    <div
                        v-if="stay.images.length > 1"
                        class="gallery-grid mt-8"
                    >
                        <button
                            v-for="photo in stay.images.slice(1)"
                            :key="photo.id"
                            class="gallery-photo"
                            :aria-label="
                                text('accessibility_enlarge_prefix') +
                                ' ' +
                                photo.caption
                            "
                            @click="lightbox = photo"
                        >
                            <ResortPhoto :photo="photo" />
                        </button>
                    </div>
                    <div v-if="profile.policies" class="content-block">
                        <h2>{{ text('accommodation_policies') }}</h2>
                        <p class="body-copy whitespace-pre-line">
                            {{ profile.policies }}
                        </p>
                    </div>
                </section>
                <section
                    v-if="page === 'gallery'"
                    class="resort-wrap gallery-grid"
                >
                    <figure v-for="photo in media" :key="photo.id">
                        <button
                            class="gallery-photo"
                            :aria-label="
                                text('accessibility_enlarge_prefix') +
                                ' ' +
                                photo.caption
                            "
                            @click="lightbox = photo"
                        >
                            <ResortPhoto :photo="photo" />
                        </button>
                        <figcaption>{{ photo.caption }}</figcaption>
                    </figure>
                </section>
                <section
                    v-if="page === 'plan'"
                    class="resort-wrap practical-grid"
                >
                    <div>
                        <div
                            v-if="
                                profile.address ||
                                profile.arrival ||
                                profile.map_url
                            "
                            class="content-block"
                        >
                            <h2>{{ text('plan_directions') }}</h2>
                            <p
                                v-if="profile.address"
                                class="whitespace-pre-line"
                            >
                                {{ profile.address }}
                            </p>
                            <p
                                v-if="profile.arrival"
                                class="mt-4 whitespace-pre-line"
                            >
                                {{ profile.arrival }}
                            </p>
                            <a
                                v-if="profile.map_url"
                                :href="profile.map_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="resort-link mt-5"
                                ><MapPin class="size-4" aria-hidden="true" />{{
                                    text('actions_directions')
                                }}</a
                            >
                        </div>
                        <div v-if="profile.policies" class="content-block">
                            <h2>{{ text('plan_policies') }}</h2>
                            <p class="whitespace-pre-line">
                                {{ profile.policies }}
                            </p>
                        </div>
                        <p
                            v-if="
                                !profile.address &&
                                !profile.arrival &&
                                !profile.map_url &&
                                !profile.policies
                            "
                            class="body-copy"
                        >
                            {{ text('plan_preparing') }}
                        </p>
                        <div v-if="faqs.length" class="content-block">
                            <h2>
                                {{ text('plan_faqs') }}
                            </h2>
                            <details
                                v-for="faq in faqs"
                                :key="faq.id"
                                class="faq"
                            >
                                <summary>{{ faq.question }}</summary>
                                <p class="whitespace-pre-line">
                                    {{ faq.answer }}
                                </p>
                            </details>
                        </div>
                    </div>
                    <aside class="booking-aside">
                        <h2>
                            {{ text('plan_help_heading') }}
                        </h2>
                        <p>
                            {{ text('plan_help_description') }}
                        </p>
                        <Link
                            :href="routes.contact(locale)"
                            class="resort-button"
                            >{{ text('actions_enquiry') }}</Link
                        >
                    </aside>
                </section>
                <section
                    v-if="page === 'booking' || page === 'contact'"
                    class="resort-wrap request-grid"
                >
                    <RequestForm
                        :key="locale + page"
                        :locale="locale"
                        :copy="copy"
                        :booking="page === 'booking'"
                        :accommodations="accommodations"
                        :token="submissionToken ?? ''"
                        :selected="selectedAccommodation"
                        :today="today"
                        :enabled="acceptsRequests"
                        :has-privacy="!!profile.privacy"
                    />
                    <aside class="request-aside">
                        <p class="eyebrow">
                            {{ text('requests_steps_label') }}
                        </p>
                        <ol>
                            <li>
                                <span>01</span>
                                <div>
                                    <h2>
                                        {{ text('requests_step_one_title') }}
                                    </h2>
                                    <p>
                                        {{
                                            text(
                                                'requests_step_one_description',
                                            )
                                        }}
                                    </p>
                                </div>
                            </li>
                            <li>
                                <span>02</span>
                                <div>
                                    <h2>
                                        {{ text('requests_step_two_title') }}
                                    </h2>
                                    <p>
                                        {{
                                            text(
                                                'requests_step_two_description',
                                            )
                                        }}
                                    </p>
                                </div>
                            </li>
                            <li>
                                <span>03</span>
                                <div>
                                    <h2>
                                        {{ text('requests_step_three_title') }}
                                    </h2>
                                    <p>
                                        {{
                                            text(
                                                'requests_step_three_description',
                                            )
                                        }}
                                    </p>
                                </div>
                            </li>
                        </ol>
                        <div
                            v-if="profile.email || profile.phone"
                            class="contact-details"
                        >
                            <h2>
                                {{ text('contact_heading') }}
                            </h2>
                            <a
                                v-if="profile.phone"
                                :href="
                                    'tel:' + profile.phone.replace(/\s/g, '')
                                "
                                ><bdi dir="ltr">{{ profile.phone }}</bdi></a
                            ><a
                                v-if="profile.email"
                                :href="'mailto:' + profile.email"
                                ><bdi dir="ltr">{{ profile.email }}</bdi></a
                            ><a
                                v-if="profile.whatsapp"
                                :href="
                                    'https://wa.me/' +
                                    profile.whatsapp.replace(/\D/g, '')
                                "
                                target="_blank"
                                rel="noopener noreferrer"
                                class="resort-link"
                                >{{ text('actions_whatsapp') }}</a
                            >
                            <p v-if="profile.response_hours">
                                {{ profile.response_hours }}
                            </p>
                        </div>
                    </aside>
                </section>
                <section v-if="page === 'privacy'" class="resort-wrap">
                    <div class="body-copy whitespace-pre-line">
                        {{ profile.privacy }}
                    </div>
                </section>
                <section
                    v-if="page === 'received' && receipt"
                    class="resort-wrap"
                >
                    <div class="receipt">
                        <Check class="size-10" aria-hidden="true" />
                        <p>
                            {{
                                systemText(
                                    'Our team will review your details and get in touch. This acknowledgement does not confirm a reservation.',
                                    'سيراجع فريقنا بياناتك ويتواصل معك. هذا الإشعار لا يُعد تأكيداً للحجز.',
                                )
                            }}
                        </p>
                        <p class="eyebrow">
                            {{ text('received_reference') }}
                        </p>
                        <bdi class="reference" dir="ltr">{{
                            receipt.reference
                        }}</bdi>
                        <p v-if="profile.response_hours">
                            {{ profile.response_hours }}
                        </p>
                        <Link
                            :href="routes.home(locale)"
                            class="resort-button"
                            >{{ text('actions_home') }}</Link
                        >
                    </div>
                </section>
            </template>
        </main>
        <footer class="resort-footer resort-wrap">
            <div class="footer-top">
                <div>
                    <p class="footer-brand">{{ brand }}</p>
                    <p>
                        {{ text('footer_welcome') }}
                    </p>
                </div>
                <nav :aria-label="text('accessibility_footer')">
                    <Link :href="routes.contact(locale)">{{
                        text('actions_contact')
                    }}</Link
                    ><Link
                        v-if="profile.privacy"
                        :href="routes.privacy(locale)"
                        >{{ text('actions_privacy') }}</Link
                    ><a
                        v-if="profile.instagram_url"
                        :href="profile.instagram_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        >{{ text('footer_instagram') }}</a
                    ><a
                        v-if="profile.facebook_url"
                        :href="profile.facebook_url"
                        target="_blank"
                        rel="noopener noreferrer"
                        >{{ text('footer_facebook') }}</a
                    >
                </nav>
            </div>
            <div class="footer-bottom">
                <span>© {{ new Date().getFullYear() }} {{ brand }}</span
                ><span>{{ text('footer_tagline') }}</span>
            </div>
        </footer>
        <Dialog
            :open="!!lightbox"
            @update:open="
                (open) => {
                    if (!open) lightbox = null;
                }
            "
            ><DialogContent
                v-if="lightbox"
                :show-close-button="false"
                class="resort-lightbox max-h-[95dvh] overflow-auto sm:max-w-5xl"
                :dir="ar ? 'rtl' : 'ltr'"
                :lang="locale"
                ><div class="flex items-center justify-between gap-4">
                    <DialogTitle>{{ lightbox.caption }}</DialogTitle
                    ><DialogClose as-child
                        ><Button
                            variant="outline"
                            :aria-label="text('accessibility_close_photo')"
                            ><X class="size-5" /></Button
                    ></DialogClose>
                </div>
                <img
                    :src="lightbox.large"
                    :alt="lightbox.alt"
                    :width="lightbox.width"
                    :height="lightbox.height"
                    class="max-h-[75dvh] w-full object-contain"
                /><DialogDescription>{{
                    lightbox.alt
                }}</DialogDescription></DialogContent
            ></Dialog
        >
    </div>
</template>
