<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import RedesignBookingPanel from '@/components/website/RedesignBookingPanel.vue';
import RedesignEnquiry from '@/components/website/RedesignEnquiry.vue';
import RedesignStayCards from '@/components/website/RedesignStayCards.vue';
import { useRedesign } from '@/composables/useRedesign';
const {
    props,
    photos,
    photoIndex,
    photoOpen,
    returnFocus,
    label,
    text,
    image,
    link,
    profile,
    whatsappUrl,
    featuredStays,
    filteredStays,
    activeFilter,
    setFilter,
    matches,
    openBooking,
    enquire,
    viewPhoto,
    copyContact,
    activePanel,
    activatePanel,
    scrollCarousel,
} = useRedesign('gallery');
const visibleGallery = computed(() =>
    props.redesign.gallery.filter((photo) =>
        matches(photo.category ?? 'resort'),
    ),
);
function openGallery(index: number): void {
    returnFocus.value =
        document.activeElement instanceof HTMLElement
            ? document.activeElement
            : null;
    photos.value = visibleGallery.value;
    photoIndex.value = index;
    photoOpen.value = true;
}
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 1.7778">
                <ResortPhoto
                    v-if="image('bridge')"
                    :photo="image('bridge')!"
                    :fill="false"
                    sizes="100vw"
                    eager
                />
                <div class="wrap hero-copy">
                    <span class="kicker">
                        {{ text('0_section_0_div_1_div_0_span_0') }}
                    </span>
                    <h1>
                        {{ text('0_section_0_div_1_div_1_h1_0') }}
                    </h1>
                </div>
            </div>
        </section>
        <nav class="crumbs" :aria-label="text('1_nav_attr_aria_label')">
            <div class="wrap">
                <ol>
                    <li>
                        <Link :href="link('index.html')">
                            {{ text('1_nav_0_div_0_ol_0_li_0_a_0') }}
                        </Link>
                    </li>
                    <li>
                        <span aria-current="page">
                            {{ text('1_nav_0_div_0_ol_1_li_0_span_0') }}
                        </span>
                    </li>
                </ol>
            </div>
        </nav>
        <section>
            <div class="wrap">
                <div class="filters" role="group">
                    <button
                        @click="setFilter('all')"
                        :aria-pressed="activeFilter === 'all'"
                        type="button"
                    >
                        {{ text('2_section_0_div_0_div_0_button_0') }}
                    </button>
                    <button
                        @click="setFilter('resort')"
                        :aria-pressed="activeFilter === 'resort'"
                        type="button"
                    >
                        {{ text('2_section_0_div_0_div_1_button_0') }}
                    </button>
                    <button
                        @click="setFilter('villas')"
                        :aria-pressed="activeFilter === 'villas'"
                        type="button"
                    >
                        {{ text('2_section_0_div_0_div_2_button_0') }}
                    </button>
                    <button
                        @click="setFilter('dining')"
                        :aria-pressed="activeFilter === 'dining'"
                        type="button"
                    >
                        {{ text('2_section_0_div_0_div_3_button_0') }}
                    </button>
                    <button
                        @click="setFilter('sport')"
                        :aria-pressed="activeFilter === 'sport'"
                        type="button"
                    >
                        {{ text('2_section_0_div_0_div_4_button_0') }}
                    </button>
                    <button
                        @click="setFilter('desert')"
                        :aria-pressed="activeFilter === 'desert'"
                        type="button"
                    >
                        {{ text('2_section_0_div_0_div_5_button_0') }}
                    </button>
                </div>
                <div class="masonry" id="gal">
                    <button
                        v-for="(photo, index) in visibleGallery"
                        :key="photo.id"
                        type="button"
                        :aria-label="photo.caption"
                        @click="openGallery(index)"
                    >
                        <ResortPhoto
                            :photo="photo"
                            :fill="false"
                            sizes="(max-width:700px) 92vw, 33vw"
                        />
                    </button>
                </div>
                <p v-if="!visibleGallery.length" role="status">
                    {{
                        label(
                            'No photographs in this category. Choose another filter.',
                            'لا توجد صور في هذه الفئة. اختر فئة أخرى.',
                        )
                    }}
                </p>
            </div>
        </section>
        <section class="sand">
            <div class="wrap cta">
                <span class="kicker">
                    {{ text('3_section_0_div_0_span_0') }}
                </span>
                <h2>
                    {{ text('3_section_0_div_1_h2_0') }}
                </h2>
                <p class="lede">
                    {{ text('3_section_0_div_2_p_0') }}
                </p>
                <div class="row">
                    <button
                        @click="openBooking('stay')"
                        class="btn btn-palm"
                        type="button"
                    >
                        {{ text('3_section_0_div_3_div_0_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </button>
                    <Link :href="link('location.html')" class="btn btn-ghost">
                        {{ text('3_section_0_div_3_div_1_a_0') }}
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
