import { computed, inject, ref, type InjectionKey, type Ref } from 'vue';
import * as routes from '@/routes/website';
import type { Accommodation, ResortImage, WebsiteProps } from '@/types/website';

export type BookingMode = 'stay' | 'group' | 'team' | 'school';
export interface BookingDraft {
    mode: BookingMode;
    accommodation: string;
    arrival: string;
    departure: string;
    party: string;
    audience: string;
    experience: string;
}
export interface RedesignContext {
    props: WebsiteProps;
    root: Ref<HTMLElement | null>;
    bookingOpen: Ref<boolean>;
    enquiryOpen: Ref<boolean>;
    draft: BookingDraft;
    photos: Ref<ResortImage[]>;
    photoIndex: Ref<number>;
    photoOpen: Ref<boolean>;
    returnFocus: Ref<HTMLElement | null>;
}
export const redesignKey: InjectionKey<RedesignContext> =
    Symbol('website-redesign');

export function useRedesign(page = 'home') {
    const context = inject(redesignKey);
    if (!context) throw new Error('Website redesign context is unavailable.');
    const { props, root, draft } = context;
    const ar = computed(() => props.locale === 'ar');
    const label = (en: string, arabic: string) => (ar.value ? arabic : en);
    const activeFilter = ref('all');
    const activePanel = ref(0);
    const profile = computed(() => props.profile);
    const text = (key: string) => props.redesign.pages[page]?.copy[key] ?? '';
    const image = (key: string) => props.redesign.images[key];
    const whatsappUrl = computed(() =>
        props.profile.whatsapp
            ? 'https://wa.me/' + props.profile.whatsapp.replace(/\D/g, '')
            : undefined,
    );
    const filteredStays = computed(() =>
        props.accommodations.filter(
            (stay) =>
                activeFilter.value === 'all' ||
                stay.categories?.includes(activeFilter.value),
        ),
    );
    const featuredStays = computed(() =>
        ['oman-villa', 'majan-villa', 'luxury-1-br-villa'].flatMap((slug) =>
            props.accommodations.filter((stay) => stay.slug === slug),
        ),
    );
    const sourceStays: Record<string, string> = {
        'villa-oman': 'oman-villa',
        'villa-majan': 'majan-villa',
        'villa-salalah': 'salalah-villa',
        'villa-mazon': 'mazon-villa',
        'villa-luxury-one-bedroom': 'luxury-1-br-villa',
        'standard-room': 'standard-room',
    };
    function hasPage(filename: string): boolean {
        const file = filename.split('#')[0].replace(/\.html$/, '');
        if (file === 'villas') return props.accommodations.length > 0;
        if (file === 'gallery') return props.media.length > 0;
        if (file === 'school-trips') return !!props.redesign.pages.schools;
        if (['experiences', 'dining', 'sport'].includes(file))
            return !!props.redesign.pages[file];
        if (sourceStays[file])
            return props.accommodations.some(
                (stay) => stay.slug === sourceStays[file],
            );
        return true;
    }
    function link(filename: string): string {
        const [path, hash] = filename.split('#');
        const file = path
            .replace(/^\.\.\//, '')
            .replace(/^ar\//, '')
            .replace(/\.html$/, '');
        const route = {
            index: routes.home,
            villas: routes.accommodations,
            experiences: routes.experiences,
            dining: routes.dining,
            sport: routes.sport,
            'school-trips': routes.schools,
            gallery: routes.gallery,
            location: routes.contact,
        }[file];
        const url = route
            ? route(props.locale).url
            : routes.accommodation({
                  locale: props.locale,
                  slug: sourceStays[file] ?? file,
              }).url;
        return url + (hash ? '#' + hash : '');
    }
    const stayLink = (stay: Accommodation) =>
        routes.accommodation({ locale: props.locale, slug: stay.slug }).url;
    function openBooking(mode: BookingMode = 'stay', slug?: string): void {
        context!.returnFocus.value =
            document.activeElement instanceof HTMLElement
                ? document.activeElement
                : null;
        draft.mode = mode;
        draft.audience =
            mode === 'team'
                ? 'team'
                : mode === 'school'
                  ? 'school'
                  : mode === 'group'
                    ? 'group'
                    : 'stay';
        draft.accommodation = slug
            ? String(
                  props.accommodations.find((stay) => stay.slug === slug)?.id ??
                      '',
              )
            : '';
        draft.experience = '';
        context!.bookingOpen.value = true;
    }
    function enquire(experience = ''): void {
        context!.returnFocus.value =
            document.activeElement instanceof HTMLElement
                ? document.activeElement
                : null;
        draft.experience =
            Object.entries(props.redesign.pages.experiences?.copy ?? {}).find(
                ([key, value]) => key.endsWith('_h3_0') && value === experience,
            )?.[0] ?? experience;
        context!.enquiryOpen.value = true;
    }
    function viewPhoto(key: string, keys: string[]): void {
        context!.returnFocus.value =
            document.activeElement instanceof HTMLElement
                ? document.activeElement
                : null;
        const seen = new Set<number>();
        context!.photos.value = keys.flatMap((k) => {
            const photo = image(k);
            if (
                !photo ||
                seen.has(photo.id) ||
                !matches(photo.category ?? 'resort')
            )
                return [];
            seen.add(photo.id);
            return [photo];
        });
        const index = context!.photos.value.findIndex(
            (photo) => photo.id === image(key)?.id,
        );
        if (index < 0) return;
        context!.photoIndex.value = index;
        context!.photoOpen.value = true;
    }
    const setFilter = (filter: string) => {
        activeFilter.value = filter;
    };
    const matches = (categories: string) =>
        activeFilter.value === 'all' ||
        categories.split(' ').includes(activeFilter.value);
    const activatePanel = (index: number) => {
        if (window.matchMedia('(hover:hover) and (min-width:861px)').matches)
            activePanel.value = index;
    };
    const carouselAtStart = ref(true);
    const carouselAtEnd = ref(false);
    function updateCarousel(): void {
        const track = root.value?.querySelector<HTMLElement>('#track');
        if (!track) return;
        const position = Math.abs(track.scrollLeft);
        carouselAtStart.value = position < 8;
        carouselAtEnd.value =
            position + track.clientWidth >= track.scrollWidth - 8;
    }
    function scrollCarousel(direction: number): void {
        const track = root.value?.querySelector<HTMLElement>('#track');
        track?.scrollBy({
            left:
                direction *
                (ar.value ? -1 : 1) *
                ((track
                    .querySelector<HTMLElement>('.xc')
                    ?.getBoundingClientRect().width ?? 300) +
                    20),
            behavior: window.matchMedia('(prefers-reduced-motion:reduce)')
                .matches
                ? 'instant'
                : 'smooth',
        });
    }
    const copied = ref(false);
    const copyStatus = ref('');
    async function copyContact(
        kind: 'email' | 'phone' | 'whatsapp',
    ): Promise<void> {
        const value = props.profile[kind];
        if (!value || !navigator.clipboard) {
            copyStatus.value = label(
                'Copy is unavailable. Select the contact details to copy them.',
                'تعذر النسخ. حدد بيانات التواصل لنسخها.',
            );
            return;
        }
        try {
            await navigator.clipboard.writeText(value);
            copied.value = true;
            copyStatus.value = label('Copied', 'تم النسخ');
        } catch {
            copied.value = false;
            copyStatus.value = label(
                'Copy is unavailable. Select the contact details to copy them.',
                'تعذر النسخ. حدد بيانات التواصل لنسخها.',
            );
        }
    }
    return {
        ...context,
        ar,
        label,
        profile,
        text,
        image,
        link,
        hasPage,
        stayLink,
        stayFacts: accommodationFacts,
        whatsappUrl,
        filteredStays,
        featuredStays,
        activeFilter,
        activePanel,
        setFilter,
        matches,
        activatePanel,
        scrollCarousel,
        updateCarousel,
        carouselAtStart,
        carouselAtEnd,
        openBooking,
        enquire,
        viewPhoto,
        copied,
        copyStatus,
        copyContact,
    };
}

export function accommodationFacts(
    stay: Accommodation | null,
): { label: string; value: string }[] {
    const labels = [
        'Ideal for',
        'Bedrooms',
        'Type',
        'Pool',
        'مثالية لـ',
        'غرف النوم',
        'النوع',
        'المسبح',
    ];
    return (stay?.facts ?? '')
        .split('\n')
        .filter(Boolean)
        .map((line) => {
            const colon = line.indexOf(':');
            if (colon >= 0)
                return {
                    label: line.slice(0, colon).trim(),
                    value: line.slice(colon + 1).trim(),
                };
            const label = labels.find((label) => line.startsWith(label + ' '));
            return {
                label: label ?? '',
                value: label ? line.slice(label.length).trim() : line,
            };
        });
}
