<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ArrowRight, Menu, X, Download } from '@lucide/vue';
import {
    computed,
    onMounted,
    onUnmounted,
    provide,
    reactive,
    ref,
    watch,
} from 'vue';
import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogDescription,
    DialogClose,
} from '@/components/ui/dialog';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import RedesignIcons from '@/components/website/RedesignIcons.vue';
import RedesignHome from '@/components/website/RedesignHome.vue';
import RedesignStays from '@/components/website/RedesignStays.vue';
import RedesignDining from '@/components/website/RedesignDining.vue';
import RedesignExperiences from '@/components/website/RedesignExperiences.vue';
import RedesignSport from '@/components/website/RedesignSport.vue';
import RedesignSchools from '@/components/website/RedesignSchools.vue';
import RedesignLocation from '@/components/website/RedesignLocation.vue';
import RedesignGallery from '@/components/website/RedesignGallery.vue';
import RedesignStayCards from '@/components/website/RedesignStayCards.vue';
import RedesignBookingPanel from '@/components/website/RedesignBookingPanel.vue';
import RedesignEnquiry from '@/components/website/RedesignEnquiry.vue';
import {
    accommodationFacts,
    redesignKey,
    type BookingDraft,
    type BookingMode,
} from '@/composables/useRedesign';
import type { ResortImage, WebsiteProps } from '@/types/website';
import * as routes from '@/routes/website';
import '../../../css/website-v2.css';
const props = defineProps<WebsiteProps>();
const root = ref<HTMLElement | null>(null);
const returnFocus = ref<HTMLElement | null>(null);
const ar = computed(() => props.locale === 'ar');
const label = (en: string, arabic: string) => (ar.value ? arabic : en);
const menuOpen = ref(false),
    bookingOpen = ref(false),
    enquiryOpen = ref(false),
    photoOpen = ref(false);
const photos = ref<ResortImage[]>([]),
    photoIndex = ref(0);
const selectedPhoto = computed(() => photos.value[photoIndex.value]);
const draft = reactive<BookingDraft>({
    mode: 'stay',
    accommodation: props.selectedAccommodation ?? '',
    arrival: '',
    departure: '',
    party: '2 adults',
    audience: 'stay',
    experience: '',
});
provide(redesignKey, {
    props,
    root,
    bookingOpen,
    enquiryOpen,
    draft,
    photos,
    photoIndex,
    photoOpen,
    returnFocus,
});
const solid = ref(false),
    mobileBar = ref(false);
const nav = computed(() => [
    {
        key: 'home',
        title: label('Home', 'الرئيسية'),
        href: routes.home(props.locale).url,
    },
    ...(props.accommodations.length
        ? [
              {
                  key: 'accommodation',
                  title: label('Villas & Rooms', 'الفيلات والغرف'),
                  href: routes.accommodations(props.locale).url,
              },
          ]
        : []),
    ...(
        ['experiences', 'dining', 'sport', 'gallery', 'contact'] as const
    ).flatMap((key) => {
        const titles = {
            experiences: label('Experiences', 'التجارب'),
            dining: label('Dining', 'المطاعم'),
            sport: label('Sport & Wellbeing', 'الرياضة والعافية'),
            gallery: label('Gallery', 'الصور'),
            contact: label('Location & Contact', 'الموقع والتواصل'),
        };
        return props.redesign.pages[key] ||
            (key === 'gallery' && props.media.length) ||
            key === 'contact'
            ? [{ key, title: titles[key], href: routes[key](props.locale).url }]
            : [];
    }),
]);
const pageKey = computed(() =>
    props.page === 'accommodation'
        ? 'accommodations'
        : props.page === 'stay'
          ? (props.stay?.sourcePage ?? '')
          : props.page,
);
const pageContent = computed(() => props.redesign.pages[pageKey.value]);
const title = computed(() =>
    props.page === 'stay'
        ? `${props.stay?.title} | ${props.copy.brand_name}`
        : (pageContent.value?.title ??
          props.copy[props.page + '_title'] ??
          props.copy.brand_name),
);
const alternateWithSelection = computed(() => {
    const url = new URL(props.alternateUrl);
    if (url.searchParams.has('signature')) return props.alternateUrl;
    if (draft.accommodation)
        url.searchParams.set('accommodation', draft.accommodation);
    return url.toString();
});
const description = computed(() =>
    props.page === 'stay'
        ? props.stay?.shortDescription
        : (pageContent.value?.description ?? props.profile.seo_description),
);
const hasHero = computed(() =>
    [
        'home',
        'accommodation',
        'experiences',
        'dining',
        'sport',
        'schools',
        'gallery',
        'contact',
    ].includes(props.page),
);
const whatsapp = computed(() =>
    props.profile.whatsapp
        ? 'https://wa.me/' + props.profile.whatsapp.replace(/\D/g, '')
        : null,
);
const components = {
    home: RedesignHome,
    accommodation: RedesignStays,
    dining: RedesignDining,
    experiences: RedesignExperiences,
    sport: RedesignSport,
    schools: RedesignSchools,
    contact: RedesignLocation,
    gallery: RedesignGallery,
};
const currentComponent = computed(
    () => components[props.page as keyof typeof components],
);
const detailFacts = computed(() => accommodationFacts(props.stay));
const structuredData = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Resort',
        name: props.copy.brand_name,
        url: routes.home(props.locale).url,
        telephone: props.profile.phone ?? undefined,
        email: props.profile.email ?? undefined,
        image: props.redesign.images['hero-pool2']?.large,
        address: props.profile.address || undefined,
    }).replace(/</g, '\\u003c'),
);
function captureFocus(): void {
    returnFocus.value =
        document.activeElement instanceof HTMLElement
            ? document.activeElement
            : null;
}
function openMenu(): void {
    captureFocus();
    menuOpen.value = true;
}
function restoreDialogFocus(event: Event): void {
    event.preventDefault();
    if (
        !menuOpen.value &&
        !bookingOpen.value &&
        !enquiryOpen.value &&
        !photoOpen.value
    ) {
        if (returnFocus.value?.isConnected) returnFocus.value.focus();
        else root.value?.querySelector<HTMLElement>('#main')?.focus();
    }
}
function openBooking(mode: BookingMode = 'stay'): void {
    if (!menuOpen.value) captureFocus();
    menuOpen.value = false;
    draft.mode = mode;
    draft.audience =
        mode === 'team'
            ? 'team'
            : mode === 'school'
              ? 'school'
              : mode === 'group'
                ? 'group'
                : 'stay';
    draft.experience = '';
    bookingOpen.value = true;
}
function photoMove(step: number): void {
    photoIndex.value =
        (photoIndex.value + step + photos.value.length) % photos.value.length;
}
function showStayPhoto(index: number): void {
    captureFocus();
    photos.value = props.stay?.images ?? [];
    photoIndex.value = index;
    photoOpen.value = true;
}
const onScroll = () => {
    solid.value = window.scrollY > 40;
    mobileBar.value = window.scrollY > window.innerHeight * 0.6;
};
const onPhotoKey = (event: KeyboardEvent) => {
    if (!photoOpen.value) return;
    if (['ArrowRight', 'ArrowLeft'].includes(event.key)) event.preventDefault();
    if (event.key === 'ArrowRight') photoMove(ar.value ? -1 : 1);
    if (event.key === 'ArrowLeft') photoMove(ar.value ? 1 : -1);
};
let swipeStart: number | null = null;
const onSwipeStart = (event: PointerEvent) => {
    if (!event.isPrimary || event.button !== 0) return;
    swipeStart = event.clientX;
    (event.currentTarget as HTMLElement).setPointerCapture(event.pointerId);
};
const onSwipe = (event: PointerEvent) => {
    if (swipeStart === null) return;
    const distance = event.clientX - swipeStart;
    swipeStart = null;
    if (Math.abs(distance) > 40) photoMove(distance < 0 !== ar.value ? 1 : -1);
};
onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('keydown', onPhotoKey);
});
onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
    window.removeEventListener('keydown', onPhotoKey);
});
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
watch(
    () => props.canonicalUrl,
    () => {
        menuOpen.value = false;
        bookingOpen.value = false;
        enquiryOpen.value = false;
        photoOpen.value = false;
    },
);
</script>
<template>
    <div
        ref="root"
        class="website-v2"
        :class="{ 'has-hero': hasHero }"
        :dir="ar ? 'rtl' : 'ltr'"
        :lang="locale"
    >
        <Head :title="title">
            <meta
                head-key="description"
                name="description"
                :content="description"
            />
            <meta
                head-key="robots"
                name="robots"
                :content="
                    indexable && !isPreview && page !== 'received'
                        ? 'index,follow'
                        : 'noindex,nofollow'
                "
            />
            <link head-key="canonical" rel="canonical" :href="canonicalUrl" />
            <link
                head-key="alternate-current"
                rel="alternate"
                :hreflang="locale"
                :href="canonicalUrl"
            />
            <link
                head-key="alternate-other"
                rel="alternate"
                :hreflang="ar ? 'en' : 'ar'"
                :href="alternateWithSelection"
            />
            <meta head-key="og-title" property="og:title" :content="title" />
            <meta
                head-key="og-description"
                property="og:description"
                :content="description"
            />
            <link
                head-key="favicon"
                rel="icon"
                :href="(branding.light ?? redesign.images['mark-green'])?.src"
            />
            <meta
                head-key="og-locale"
                property="og:locale"
                :content="ar ? 'ar_OM' : 'en_GB'"
            />
            <meta head-key="og-url" property="og:url" :content="canonicalUrl" />
            <meta
                head-key="og-image"
                property="og:image"
                :content="redesign.images['hero-pool2']?.large"
            />
            <component
                :is="'script'"
                type="application/ld+json"
                head-key="resort-structured-data"
                >{{ structuredData }}</component
            >
        </Head>
        <a class="skip" href="#main">{{ copy.accessibility_skip }}</a>
        <div v-if="isPreview" class="v2-preview" role="status">
            {{
                label(
                    'Staff preview · unpublished content · requests disabled',
                    'معاينة الموظفين · محتوى غير منشور · الطلبات معطلة',
                )
            }}
        </div>
        <RedesignIcons />
        <header class="top" :class="{ solid: solid || !hasHero }">
            <div class="wrap bar">
                <div class="bar-l">
                    <button
                        class="menu-btn"
                        type="button"
                        :aria-expanded="menuOpen"
                        aria-controls="v2-menu"
                        :aria-label="label('Open menu', 'فتح القائمة')"
                        @click="openMenu()"
                    >
                        <Menu /><span>{{ label('Menu', 'القائمة') }}</span>
                    </button>
                </div>
                <Link class="brand" :href="routes.home(locale).url"
                    ><ResortPhoto
                        v-if="branding.light || redesign.images['mark-green']"
                        class="g"
                        :photo="branding.light ?? redesign.images['mark-green']"
                        :fill="false"
                    /><ResortPhoto
                        v-if="branding.dark || redesign.images['mark-white']"
                        class="w"
                        :photo="branding.dark ?? redesign.images['mark-white']"
                        :fill="false"
                    /><span
                        ><b>{{ label('Jawharat Bidiyah', 'جوهرة بدية') }}</b
                        ><small>{{
                            label('Resort · Oman', 'منتجع · عُمان')
                        }}</small></span
                    ></Link
                >
                <div class="bar-r">
                    <Link
                        class="lang"
                        :href="alternateWithSelection"
                        :lang="ar ? 'en' : 'ar'"
                        >{{ ar ? 'English' : 'العربية' }}</Link
                    ><button
                        class="btn btn-brass"
                        type="button"
                        @click="openBooking()"
                    >
                        {{ label('Book', 'احجز') }}
                    </button>
                </div>
            </div>
        </header>
        <main id="main" tabindex="-1">
            <component
                :is="currentComponent"
                v-if="currentComponent"
                :key="locale + '-' + page"
            />
            <template v-else-if="page === 'stay' && stay">
                <nav
                    class="crumbs"
                    :aria-label="label('Breadcrumb', 'مسار التنقل')"
                >
                    <div class="wrap">
                        <ol>
                            <li>
                                <Link :href="routes.home(locale).url">{{
                                    label('Home', 'الرئيسية')
                                }}</Link>
                            </li>
                            <li>
                                <Link
                                    :href="routes.accommodations(locale).url"
                                    >{{
                                        label(
                                            'Villas & Rooms',
                                            'الفيلات والغرف',
                                        )
                                    }}</Link
                                >
                            </li>
                            <li aria-current="page">{{ stay.title }}</li>
                        </ol>
                    </div>
                </nav>
                <section style="padding-top: 0">
                    <div class="wrap">
                        <div class="vtitle">
                            <span class="kicker">{{
                                detailFacts.find((fact) =>
                                    ['Type', 'النوع'].includes(fact.label),
                                )?.value
                            }}</span>
                            <h1>{{ stay.title }}</h1>
                            <p class="lede">{{ stay.shortDescription }}</p>
                        </div>
                        <div class="vimg">
                            <span v-if="stay.badge" class="badge">{{
                                stay.badge
                            }}</span>
                            <div
                                class="vgal"
                                :class="'n' + Math.min(stay.images.length, 3)"
                            >
                                <button
                                    v-for="(photo, index) in stay.images"
                                    :key="photo.id"
                                    type="button"
                                    :aria-label="photo.caption"
                                    @click="showStayPhoto(index)"
                                >
                                    <ResortPhoto :photo="photo" :fill="false" />
                                </button>
                            </div>
                        </div>
                        <div class="vbody">
                            <div class="prose">
                                <span class="kicker">{{
                                    label('Overview', 'نظرة عامة')
                                }}</span>
                                <p
                                    v-for="paragraph in stay.description.split(
                                        '\n\n',
                                    )"
                                    :key="paragraph"
                                >
                                    {{ paragraph }}
                                </p>
                                <h2>{{ label('Features', 'المزايا') }}</h2>
                                <ul class="amen">
                                    <li
                                        v-for="feature in stay.inclusions.split(
                                            '\n',
                                        )"
                                        :key="feature"
                                    >
                                        {{ feature }}
                                    </li>
                                </ul>
                            </div>
                            <aside class="aside">
                                <h3>
                                    {{ label('At a glance', 'لمحة سريعة') }}
                                </h3>
                                <dl>
                                    <div
                                        v-for="fact in detailFacts"
                                        :key="fact.label + fact.value"
                                    >
                                        <dt v-if="fact.label">
                                            {{ fact.label }}
                                        </dt>
                                        <dd>{{ fact.value }}</dd>
                                    </div>
                                </dl>
                                <p v-if="stay.capacity">
                                    {{
                                        stay.capacityBasis === 'adults'
                                            ? label(
                                                  'Maximum adults',
                                                  'الحد الأقصى للبالغين',
                                              )
                                            : label(
                                                  'Maximum guests',
                                                  'الحد الأقصى للضيوف',
                                              )
                                    }}: {{ stay.capacity }}
                                </p>
                                <button
                                    class="btn btn-palm"
                                    type="button"
                                    @click="
                                        draft.accommodation = String(stay.id);
                                        openBooking();
                                    "
                                >
                                    {{
                                        label(
                                            'Check availability',
                                            'استفسر عن التوفر',
                                        )
                                    }}</button
                                ><small>{{
                                    label(
                                        'Our team confirms availability and rates after your enquiry.',
                                        'يؤكد فريقنا التوفر والأسعار بعد استفسارك.',
                                    )
                                }}</small>
                            </aside>
                        </div>
                    </div>
                </section>
                <section class="sand">
                    <div class="wrap">
                        <div class="sect-head">
                            <div>
                                <span class="kicker">{{
                                    label('Villas & Rooms', 'الفيلات والغرف')
                                }}</span>
                                <h2>
                                    {{
                                        label(
                                            'Other villas & rooms',
                                            'فلل وغرف أخرى',
                                        )
                                    }}
                                </h2>
                            </div>
                            <Link
                                class="link"
                                :href="routes.accommodations(locale).url"
                                >{{
                                    label(
                                        'Discover all villas & rooms',
                                        'اكتشف جميع الفيلات والغرف',
                                    )
                                }}
                                <ArrowRight
                            /></Link>
                        </div>
                        <RedesignStayCards
                            :items="
                                accommodations
                                    .filter((item) => item.id !== stay?.id)
                                    .slice(0, 3)
                            "
                        />
                    </div>
                </section>
            </template>
            <section v-else class="legacy-content">
                <div class="wrap narrow">
                    <h1>{{ title }}</h1>
                    <RedesignEnquiry v-if="page === 'booking'" modal />
                    <template v-else-if="page === 'plan'"
                        ><h2>{{ label('Arrival', 'الوصول') }}</h2>
                        <p style="white-space: pre-line">
                            {{ profile.arrival }}
                        </p>
                        <h2>{{ label('Guest policies', 'سياسات الضيوف') }}</h2>
                        <p style="white-space: pre-line">
                            {{ profile.policies }}
                        </p>
                        <details v-for="faq in faqs" :key="faq.id">
                            <summary>{{ faq.question }}</summary>
                            <p>{{ faq.answer }}</p>
                        </details></template
                    >
                    <p
                        v-else-if="page === 'privacy'"
                        style="white-space: pre-line"
                    >
                        {{ profile.privacy }}
                    </p>
                    <template v-else-if="page === 'received'"
                        ><p>
                            {{
                                label(
                                    'Your request has been received. A reservation is confirmed only when our team sends you a confirmation.',
                                    'تم استلام طلبك. لا يُعد الحجز مؤكداً إلا بعد إرسال التأكيد من فريق المنتجع.',
                                )
                            }}
                        </p>
                        <p v-if="receipt">
                            {{ label('Reference', 'المرجع') }}:
                            <b>{{ receipt.reference }}</b>
                        </p>
                        <Link
                            class="btn btn-palm"
                            :href="routes.home(locale).url"
                            >{{
                                label('Back to home', 'العودة للرئيسية')
                            }}</Link
                        ></template
                    >
                </div>
            </section>
            <section v-if="page === 'home' && promotion" class="v2-promotion">
                <div class="wrap split">
                    <button
                        type="button"
                        :aria-label="promotion.title"
                        @click="
                            captureFocus();
                            photos = [promotion.image];
                            photoIndex = 0;
                            photoOpen = true;
                        "
                    >
                        <ResortPhoto
                            :photo="promotion.image"
                            :fill="false"
                            fit="contain"
                        />
                    </button>
                    <div class="prose">
                        <h2>{{ promotion.title }}</h2>
                        <p>{{ promotion.description }}</p>
                        <a class="btn btn-palm" :href="promotion.downloadUrl"
                            ><Download />{{
                                label('Download brochure', 'تحميل الكتيب')
                            }}</a
                        >
                    </div>
                </div>
            </section>
        </main>
        <footer class="foot">
            <div class="wrap">
                <div class="foot-grid">
                    <div>
                        <ResortPhoto
                            v-if="redesign.images['logo-white']"
                            :photo="redesign.images['logo-white']"
                            :fill="false"
                            style="max-width: 220px"
                        />
                        <p>{{ profile.address }}</p>
                    </div>
                    <div>
                        <h4>{{ label('Stay', 'الإقامة') }}</h4>
                        <Link
                            v-for="stay in accommodations"
                            :key="stay.id"
                            :href="
                                routes.accommodation({
                                    locale,
                                    slug: stay.slug,
                                }).url
                            "
                            >{{ stay.title }}</Link
                        >
                    </div>
                    <div>
                        <h4>{{ label('Discover', 'اكتشف') }}</h4>
                        <Link
                            v-for="item in nav.filter(
                                (item) => item.key !== 'home',
                            )"
                            :key="item.key"
                            :href="item.href"
                            >{{ item.title }}</Link
                        ><Link
                            v-if="redesign.pages.sport"
                            :href="routes.sport(locale).url + '#camps'"
                            >{{
                                label(
                                    'Sports teams & camps',
                                    'الفرق والمعسكرات الرياضية',
                                )
                            }}</Link
                        ><Link
                            v-if="redesign.pages.schools"
                            :href="routes.schools(locale).url"
                            >{{
                                label('School trips', 'الرحلات المدرسية')
                            }}</Link
                        ><Link :href="routes.plan(locale).url">{{
                            copy.navigation_plan
                        }}</Link>
                    </div>
                    <div>
                        <h4>{{ label('Contact', 'التواصل') }}</h4>
                        <a
                            v-if="profile.phone"
                            dir="ltr"
                            :href="'tel:' + profile.phone"
                            >{{ profile.phone }}</a
                        ><a
                            v-if="profile.email"
                            dir="ltr"
                            :href="'mailto:' + profile.email"
                            >{{ profile.email }}</a
                        ><a
                            v-if="profile.instagram_url"
                            :href="profile.instagram_url"
                            >Instagram</a
                        ><a
                            v-if="profile.facebook_url"
                            :href="profile.facebook_url"
                            >Facebook</a
                        ><Link
                            v-if="profile.privacy"
                            :href="routes.privacy(locale).url"
                            >{{ copy.actions_privacy_notice }}</Link
                        >
                    </div>
                </div>
                <div class="foot-bottom">
                    <span
                        >© {{ new Date().getFullYear() }}
                        {{ copy.brand_name }}</span
                    ><Link
                        :href="alternateWithSelection"
                        :lang="ar ? 'en' : 'ar'"
                        >{{ ar ? 'English' : 'العربية' }}</Link
                    >
                </div>
            </div>
        </footer>
        <div class="mbar" :class="{ show: mobileBar }">
            <a
                v-if="whatsapp"
                :href="whatsapp"
                target="_blank"
                rel="noopener noreferrer"
                >WhatsApp</a
            ><button type="button" @click="openBooking()">
                {{ label('Check availability', 'استفسر عن التوفر') }}
            </button>
        </div>
        <Dialog v-model:open="menuOpen"
            ><DialogContent
                id="v2-menu"
                class="v2-dialog v2-menu-dialog"
                :show-close-button="false"
                @close-auto-focus="restoreDialogFocus"
                :dir="ar ? 'rtl' : 'ltr'"
                ><div class="website-v2" :dir="ar ? 'rtl' : 'ltr'">
                    <DialogTitle class="sr-only">{{
                        label('Menu', 'القائمة')
                    }}</DialogTitle
                    ><DialogDescription class="sr-only">{{
                        copy.accessibility_navigation
                    }}</DialogDescription>
                    <div class="menu-top">
                        <DialogClose class="menu-close"
                            ><X />{{ label('Close', 'إغلاق') }}</DialogClose
                        ><Link class="lang" :href="alternateWithSelection">{{
                            ar ? 'English' : 'العربية'
                        }}</Link>
                    </div>
                    <div class="menu-grid">
                        <nav class="menu-main">
                            <Link
                                v-for="(item, index) in nav"
                                :key="item.key"
                                :href="item.href"
                                :aria-current="
                                    page === item.key ? 'page' : undefined
                                "
                                ><small>{{
                                    String(index + 1).padStart(2, '0')
                                }}</small
                                >{{ item.title }}</Link
                            >
                        </nav>
                        <div class="menu-side">
                            <div>
                                <h4>
                                    {{
                                        label(
                                            'Villas & Rooms',
                                            'الفيلات والغرف',
                                        )
                                    }}
                                </h4>
                                <ul>
                                    <li
                                        v-for="stay in accommodations"
                                        :key="stay.id"
                                    >
                                        <Link
                                            :href="
                                                routes.accommodation({
                                                    locale,
                                                    slug: stay.slug,
                                                }).url
                                            "
                                            >{{ stay.title }}</Link
                                        >
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h4>{{ label('Groups', 'المجموعات') }}</h4>
                                <Link
                                    v-if="redesign.pages.sport"
                                    :href="routes.sport(locale).url + '#camps'"
                                    >{{
                                        label(
                                            'Sports teams & camps',
                                            'الفرق والمعسكرات الرياضية',
                                        )
                                    }}</Link
                                ><Link
                                    v-if="redesign.pages.schools"
                                    :href="routes.schools(locale).url"
                                    >{{
                                        label(
                                            'School trips',
                                            'الرحلات المدرسية',
                                        )
                                    }}</Link
                                >
                            </div>
                            <div>
                                <h4>{{ label('Contact', 'التواصل') }}</h4>
                                <p dir="ltr">
                                    {{ profile.phone }}<br />{{ profile.email }}
                                </p>
                                <p>{{ profile.address }}</p>
                            </div>
                            <button
                                class="btn btn-brass"
                                type="button"
                                @click="openBooking()"
                            >
                                {{ label('Book your stay', 'احجز إقامتك') }}
                            </button>
                        </div>
                    </div>
                </div></DialogContent
            ></Dialog
        >
        <Dialog v-model:open="bookingOpen"
            ><DialogContent
                class="v2-dialog"
                :show-close-button="false"
                @close-auto-focus="restoreDialogFocus"
                :dir="ar ? 'rtl' : 'ltr'"
                ><div class="website-v2" :dir="ar ? 'rtl' : 'ltr'">
                    <div class="v2-dialog-head">
                        <DialogTitle>{{
                            label('Plan your stay', 'خطط لإقامتك')
                        }}</DialogTitle
                        ><DialogClose :aria-label="label('Close', 'إغلاق')"
                            ><X
                        /></DialogClose>
                    </div>
                    <DialogDescription>{{
                        label(
                            'Our reservations team will confirm availability.',
                            'يؤكد فريق الحجز التوفر.',
                        )
                    }}</DialogDescription
                    ><RedesignBookingPanel /></div></DialogContent
        ></Dialog>
        <Dialog v-model:open="enquiryOpen"
            ><DialogContent
                class="v2-dialog"
                :show-close-button="false"
                @close-auto-focus="restoreDialogFocus"
                :dir="ar ? 'rtl' : 'ltr'"
                ><div class="website-v2" :dir="ar ? 'rtl' : 'ltr'">
                    <div class="v2-dialog-head">
                        <DialogTitle>{{
                            label('Almost there', 'بقيت خطوة')
                        }}</DialogTitle
                        ><DialogClose :aria-label="label('Close', 'إغلاق')"
                            ><X
                        /></DialogClose>
                    </div>
                    <DialogDescription>{{
                        label(
                            'Send a request to our team, or prepare a message for WhatsApp or email.',
                            'أرسل طلباً لفريقنا أو جهّز رسالة لواتساب أو البريد الإلكتروني.',
                        )
                    }}</DialogDescription
                    ><RedesignEnquiry modal /></div></DialogContent
        ></Dialog>
        <Dialog v-model:open="photoOpen"
            ><DialogContent
                class="v2-dialog v2-photo-dialog"
                :show-close-button="false"
                @close-auto-focus="restoreDialogFocus"
                :dir="ar ? 'rtl' : 'ltr'"
                ><div class="website-v2" :dir="ar ? 'rtl' : 'ltr'">
                    <div class="v2-dialog-head">
                        <DialogTitle>{{ selectedPhoto?.caption }}</DialogTitle
                        ><DialogClose
                            :aria-label="label('Close photo', 'إغلاق الصورة')"
                            ><X
                        /></DialogClose>
                    </div>
                    <DialogDescription dir="ltr"
                        >{{ photoIndex + 1 }} /
                        {{ photos.length }}</DialogDescription
                    >
                    <div
                        class="v2-photo-stage"
                        @pointerdown="onSwipeStart"
                        @pointerup="onSwipe"
                        @pointercancel="swipeStart = null"
                        @dragstart.prevent
                    >
                        <ResortPhoto
                            v-if="selectedPhoto"
                            :photo="{
                                ...selectedPhoto,
                                src: selectedPhoto.large,
                            }"
                            :fill="false"
                            fit="contain"
                            eager
                            sizes="90vw"
                        />
                    </div>
                    <div class="v2-photo-controls">
                        <button
                            type="button"
                            :disabled="photos.length < 2"
                            :aria-label="
                                label('Previous photo', 'الصورة السابقة')
                            "
                            @click="photoMove(-1)"
                        >
                            <ArrowLeft /></button
                        ><button
                            type="button"
                            :disabled="photos.length < 2"
                            :aria-label="label('Next photo', 'الصورة التالية')"
                            @click="photoMove(1)"
                        >
                            <ArrowRight />
                        </button>
                    </div>
                    <div class="v2-photo-strip">
                        <button
                            v-for="(photo, index) in photos"
                            :key="photo.id"
                            type="button"
                            :aria-label="photo.caption"
                            :aria-current="
                                index === photoIndex ? 'true' : undefined
                            "
                            @click="photoIndex = index"
                        >
                            <ResortPhoto :photo="photo" :fill="false" />
                        </button>
                    </div></div></DialogContent
        ></Dialog>
    </div>
</template>
