<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import RedesignBookingPanel from '@/components/website/RedesignBookingPanel.vue';
import RedesignEnquiry from '@/components/website/RedesignEnquiry.vue';
import RedesignStayCards from '@/components/website/RedesignStayCards.vue';
import { useRedesign } from '@/composables/useRedesign';
const {
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
} = useRedesign('accommodations');
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 1.7778">
                <ResortPhoto
                    v-if="image('villa-pool')"
                    :photo="image('villa-pool')!"
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
                <div
                    class="prose"
                    style="margin-bottom: clamp(36px, 4vw, 56px)"
                >
                    <p class="lede">
                        {{ text('2_section_0_div_0_div_0_p_0') }}
                    </p>
                </div>
                <div class="filters" role="group">
                    <button
                        @click="setFilter('all')"
                        :aria-pressed="activeFilter === 'all'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_0_button_0') }}
                    </button>
                    <button
                        @click="setFilter('pool')"
                        :aria-pressed="activeFilter === 'pool'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_1_button_0') }}
                    </button>
                    <button
                        @click="setFilter('family')"
                        :aria-pressed="activeFilter === 'family'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_2_button_0') }}
                    </button>
                    <button
                        @click="setFilter('couples')"
                        :aria-pressed="activeFilter === 'couples'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_3_button_0') }}
                    </button>
                    <button
                        @click="setFilter('rooms')"
                        :aria-pressed="activeFilter === 'rooms'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_4_button_0') }}
                    </button>
                </div>
                <RedesignStayCards :items="filteredStays" />
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
