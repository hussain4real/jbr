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
} = useRedesign('sport');
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 1.6678">
                <ResortPhoto
                    v-if="image('stadium')"
                    :photo="image('stadium')!"
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
                    style="margin-bottom: clamp(56px, 7vw, 110px)"
                >
                    <p class="lede">
                        {{ text('2_section_0_div_0_div_0_p_0') }}
                    </p>
                </div>
                <div
                    class="split"
                    style="margin-bottom: clamp(56px, 7vw, 110px)"
                >
                    <div class="txt">
                        <span class="kicker">
                            {{ text('2_section_0_div_1_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('2_section_0_div_1_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('2_section_0_div_1_div_0_div_2_p_0') }}
                        </p>
                    </div>
                    <figure>
                        <ResortPhoto
                            v-if="image('olympic')"
                            :photo="image('olympic')!"
                            :fill="false"
                            sizes="(max-width: 900px) 92vw, 45vw"
                        />
                    </figure>
                </div>
                <div
                    class="split flip"
                    style="margin-bottom: clamp(56px, 7vw, 110px)"
                >
                    <div class="txt">
                        <span class="kicker">
                            {{ text('2_section_0_div_2_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('2_section_0_div_2_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('2_section_0_div_2_div_0_div_2_p_0') }}
                        </p>
                    </div>
                    <figure>
                        <ResortPhoto
                            v-if="image('stadium')"
                            :photo="image('stadium')!"
                            :fill="false"
                            sizes="(max-width: 900px) 92vw, 45vw"
                        />
                    </figure>
                </div>
                <div
                    class="split"
                    style="margin-bottom: clamp(56px, 7vw, 110px)"
                >
                    <div class="txt">
                        <span class="kicker">
                            {{ text('2_section_0_div_3_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('2_section_0_div_3_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('2_section_0_div_3_div_0_div_2_p_0') }}
                        </p>
                    </div>
                    <figure>
                        <ResortPhoto
                            v-if="image('hall')"
                            :photo="image('hall')!"
                            :fill="false"
                            sizes="(max-width: 900px) 92vw, 45vw"
                        />
                    </figure>
                </div>
                <div
                    class="split flip"
                    style="margin-bottom: clamp(56px, 7vw, 110px)"
                >
                    <div class="txt">
                        <span class="kicker">
                            {{ text('2_section_0_div_4_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('2_section_0_div_4_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('2_section_0_div_4_div_0_div_2_p_0') }}
                        </p>
                    </div>
                    <figure>
                        <ResortPhoto
                            v-if="image('courts')"
                            :photo="image('courts')!"
                            :fill="false"
                            sizes="(max-width: 900px) 92vw, 45vw"
                        />
                    </figure>
                </div>
            </div>
        </section>
        <section class="dark on-dark">
            <div class="wrap">
                <div class="counters">
                    <div>
                        <b>
                            {{ text('3_section_0_div_0_div_0_div_0_b_0') }}
                        </b>
                        <span>
                            {{ text('3_section_0_div_0_div_0_div_1_span_0') }}
                        </span>
                    </div>
                    <div>
                        <b>
                            {{ text('3_section_0_div_0_div_1_div_0_b_0') }}
                        </b>
                        <span>
                            {{ text('3_section_0_div_0_div_1_div_1_span_0') }}
                        </span>
                    </div>
                    <div>
                        <b>
                            {{ text('3_section_0_div_0_div_2_div_0_b_0') }}
                        </b>
                        <span>
                            {{ text('3_section_0_div_0_div_2_div_1_span_0') }}
                        </span>
                    </div>
                    <div>
                        <b>
                            {{ text('3_section_0_div_0_div_3_div_0_b_0') }}
                        </b>
                        <span>
                            {{ text('3_section_0_div_0_div_3_div_1_span_0') }}
                        </span>
                    </div>
                </div>
                <div class="disc" style="margin-top: 56px">
                    <div>
                        <h4>
                            {{ text('3_section_0_div_1_div_0_div_0_h4_0') }}
                        </h4>
                        <p>
                            {{ text('3_section_0_div_1_div_0_div_1_p_0') }}
                        </p>
                    </div>
                    <div>
                        <h4>
                            {{ text('3_section_0_div_1_div_1_div_0_h4_0') }}
                        </h4>
                        <p>
                            {{ text('3_section_0_div_1_div_1_div_1_p_0') }}
                        </p>
                    </div>
                    <div>
                        <h4>
                            {{ text('3_section_0_div_1_div_2_div_0_h4_0') }}
                        </h4>
                        <p>
                            {{ text('3_section_0_div_1_div_2_div_1_p_0') }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section id="camps">
            <div class="wrap">
                <div class="sec-head">
                    <div>
                        <span class="kicker">
                            {{ text('4_section_0_div_0_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('4_section_0_div_0_div_0_div_1_h2_0') }}
                        </h2>
                    </div>
                    <button
                        @click="openBooking('team')"
                        class="btn btn-palm"
                        type="button"
                    >
                        {{ text('4_section_0_div_0_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </button>
                </div>
                <ol class="steps">
                    <li>
                        <span class="n">
                            {{ text('4_section_0_div_1_ol_0_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('4_section_0_div_1_ol_0_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('4_section_0_div_1_ol_0_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <span class="n">
                            {{ text('4_section_0_div_1_ol_1_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('4_section_0_div_1_ol_1_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('4_section_0_div_1_ol_1_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <span class="n">
                            {{ text('4_section_0_div_1_ol_2_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('4_section_0_div_1_ol_2_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('4_section_0_div_1_ol_2_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <span class="n">
                            {{ text('4_section_0_div_1_ol_3_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('4_section_0_div_1_ol_3_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('4_section_0_div_1_ol_3_li_2_span_0') }}
                        </span>
                    </li>
                </ol>
            </div>
        </section>
        <section class="sand">
            <div class="wrap cta">
                <span class="kicker">
                    {{ text('5_section_0_div_0_span_0') }}
                </span>
                <h2>
                    {{ text('5_section_0_div_1_h2_0') }}
                </h2>
                <p class="lede">
                    {{ text('5_section_0_div_2_p_0') }}
                </p>
                <div class="row">
                    <button
                        @click="openBooking('stay')"
                        class="btn btn-palm"
                        type="button"
                    >
                        {{ text('5_section_0_div_3_div_0_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </button>
                    <Link :href="link('location.html')" class="btn btn-ghost">
                        {{ text('5_section_0_div_3_div_1_a_0') }}
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
