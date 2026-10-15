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
} = useRedesign('dining');
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 2.1651">
                <ResortPhoto
                    v-if="image('dine-hall')"
                    :photo="image('dine-hall')!"
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
                <div class="sec-head">
                    <div>
                        <span class="kicker">
                            {{ text('2_section_0_div_0_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('2_section_0_div_0_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('2_section_0_div_0_div_0_div_2_p_0') }}
                        </p>
                    </div>
                </div>
                <div class="vimg" style="margin-top: 0">
                    <div class="vgal n4" id="dgal">
                        <button
                            @click="
                                viewPhoto('dine-arches', [
                                    'dine-hall',
                                    'dine-arches',
                                    'dine-palms',
                                    'dine-buffet',
                                    'xp-chef',
                                ])
                            "
                            type="button"
                        >
                            <ResortPhoto
                                v-if="image('dine-arches')"
                                :photo="image('dine-arches')!"
                                :fill="false"
                                sizes="(max-width: 700px) 92vw, 42vw"
                                eager
                            />
                        </button>
                        <button
                            @click="
                                viewPhoto('dine-palms', [
                                    'dine-hall',
                                    'dine-arches',
                                    'dine-palms',
                                    'dine-buffet',
                                    'xp-chef',
                                ])
                            "
                            type="button"
                        >
                            <ResortPhoto
                                v-if="image('dine-palms')"
                                :photo="image('dine-palms')!"
                                :fill="false"
                                sizes="(max-width: 700px) 23vw, 20vw"
                            />
                        </button>
                        <button
                            @click="
                                viewPhoto('dine-buffet', [
                                    'dine-hall',
                                    'dine-arches',
                                    'dine-palms',
                                    'dine-buffet',
                                    'xp-chef',
                                ])
                            "
                            type="button"
                        >
                            <ResortPhoto
                                v-if="image('dine-buffet')"
                                :photo="image('dine-buffet')!"
                                :fill="false"
                                sizes="(max-width: 700px) 23vw, 20vw"
                            />
                        </button>
                        <button
                            @click="
                                viewPhoto('dine-hall', [
                                    'dine-hall',
                                    'dine-arches',
                                    'dine-palms',
                                    'dine-buffet',
                                    'xp-chef',
                                ])
                            "
                            type="button"
                        >
                            <ResortPhoto
                                v-if="image('dine-hall')"
                                :photo="image('dine-hall')!"
                                :fill="false"
                                sizes="(max-width: 700px) 23vw, 20vw"
                            />
                        </button>
                    </div>
                    <button
                        @click="
                            viewPhoto('dine-hall', [
                                'dine-hall',
                                'dine-arches',
                                'dine-palms',
                                'dine-buffet',
                                'xp-chef',
                            ])
                        "
                        class="vall"
                        type="button"
                    >
                        <svg>
                            <use href="#i-grid" />
                        </svg>
                        {{ text('2_section_0_div_1_div_1_button_1') }}
                    </button>
                </div>
            </div>
        </section>
        <section class="sand">
            <div class="wrap split" style="align-items: start">
                <div class="txt" style="position: sticky; top: 120px">
                    <p class="lede">
                        {{ text('3_section_0_div_0_div_0_p_0') }}
                    </p>
                    <figure style="margin: 8px 0 0">
                        <ResortPhoto
                            v-if="image('xp-chef')"
                            :photo="image('xp-chef')!"
                            :fill="false"
                            sizes="(max-width: 900px) 92vw, 45vw"
                        />
                    </figure>
                    <div
                        style="
                            margin-top: 20px;
                            padding: 24px;
                            background: var(--sand);
                            border-radius: 8px;
                            display: grid;
                            gap: 10px;
                        "
                    >
                        <h3>
                            {{ text('3_section_0_div_0_div_2_div_0_h3_0') }}
                        </h3>
                        <p style="color: var(--muted)">
                            {{ text('3_section_0_div_0_div_2_div_1_p_0') }}
                        </p>
                    </div>
                </div>
                <ul class="menu-list">
                    <li>
                        <h3>
                            {{ text('3_section_0_div_1_ul_0_li_0_h3_0') }}
                        </h3>
                        <p>
                            {{ text('3_section_0_div_1_ul_0_li_1_p_0') }}
                        </p>
                        <span class="t">
                            {{ text('3_section_0_div_1_ul_0_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <h3>
                            {{ text('3_section_0_div_1_ul_1_li_0_h3_0') }}
                        </h3>
                        <p>
                            {{ text('3_section_0_div_1_ul_1_li_1_p_0') }}
                        </p>
                        <span class="t">
                            {{ text('3_section_0_div_1_ul_1_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <h3>
                            {{ text('3_section_0_div_1_ul_2_li_0_h3_0') }}
                        </h3>
                        <p>
                            {{ text('3_section_0_div_1_ul_2_li_1_p_0') }}
                        </p>
                        <span class="t">
                            {{ text('3_section_0_div_1_ul_2_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <h3>
                            {{ text('3_section_0_div_1_ul_3_li_0_h3_0') }}
                        </h3>
                        <p>
                            {{ text('3_section_0_div_1_ul_3_li_1_p_0') }}
                        </p>
                        <span class="t">
                            {{ text('3_section_0_div_1_ul_3_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <h3>
                            {{ text('3_section_0_div_1_ul_4_li_0_h3_0') }}
                        </h3>
                        <p>
                            {{ text('3_section_0_div_1_ul_4_li_1_p_0') }}
                        </p>
                        <span class="t">
                            {{ text('3_section_0_div_1_ul_4_li_2_span_0') }}
                        </span>
                    </li>
                </ul>
            </div>
        </section>
        <section class="sand">
            <div class="wrap cta">
                <span class="kicker">
                    {{ text('4_section_0_div_0_span_0') }}
                </span>
                <h2>
                    {{ text('4_section_0_div_1_h2_0') }}
                </h2>
                <p class="lede">
                    {{ text('4_section_0_div_2_p_0') }}
                </p>
                <div class="row">
                    <button
                        @click="openBooking('stay')"
                        class="btn btn-palm"
                        type="button"
                    >
                        {{ text('4_section_0_div_3_div_0_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </button>
                    <Link :href="link('location.html')" class="btn btn-ghost">
                        {{ text('4_section_0_div_3_div_1_a_0') }}
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
