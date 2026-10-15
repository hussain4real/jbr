<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, onUnmounted } from 'vue';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import RedesignBookingPanel from '@/components/website/RedesignBookingPanel.vue';
import RedesignEnquiry from '@/components/website/RedesignEnquiry.vue';
import RedesignStayCards from '@/components/website/RedesignStayCards.vue';
import { useRedesign } from '@/composables/useRedesign';
const {
    text,
    image,
    link,
    hasPage,
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
    updateCarousel,
    carouselAtStart,
    carouselAtEnd,
} = useRedesign('home');
const galleryKeys = [
    'hero',
    'sun-villa',
    'stadium',
    'dusk-tree',
    'playground',
    'bridge',
];
onMounted(() => {
    updateCarousel();
    window.addEventListener('resize', updateCarousel);
});
onUnmounted(() => window.removeEventListener('resize', updateCarousel));
</script>
<template>
    <div>
        <section class="hero hero--home" id="home">
            <div class="hero-img" style="--ratio: 1.7778">
                <ResortPhoto
                    v-if="image('hero-pool2')"
                    :photo="image('hero-pool2')!"
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
                    <p>
                        {{ text('0_section_0_div_1_div_2_p_0') }}
                    </p>
                </div>
            </div>
            <RedesignBookingPanel inline />
        </section>
        <div class="proof">
            <div class="wrap">
                <ul>
                    <li>
                        <svg>
                            <use href="#i-pool" />
                        </svg>
                        {{ text('1_div_0_div_0_ul_0_li_1') }}
                    </li>
                    <li>
                        <svg>
                            <use href="#i-stadium" />
                        </svg>
                        {{ text('1_div_0_div_0_ul_1_li_1') }}
                    </li>
                    <li>
                        <svg>
                            <use href="#i-dune" />
                        </svg>
                        {{ text('1_div_0_div_0_ul_2_li_1') }}
                    </li>
                    <li>
                        <svg>
                            <use href="#i-clock" />
                        </svg>
                        {{ text('1_div_0_div_0_ul_3_li_1') }}
                    </li>
                </ul>
            </div>
        </div>
        <section>
            <div class="wrap split">
                <div class="txt">
                    <span class="kicker">
                        {{ text('2_section_0_div_0_div_0_span_0') }}
                    </span>
                    <h2>
                        {{ text('2_section_0_div_0_div_1_h2_0') }}
                    </h2>
                    <p class="lede">
                        {{ text('2_section_0_div_0_div_2_p_0') }}
                    </p>
                    <p class="lede">
                        {{ text('2_section_0_div_0_div_3_p_0') }}
                    </p>
                    <p
                        style="
                            display: flex;
                            gap: 14px;
                            align-items: center;
                            color: var(--muted);
                            font-size: 15px;
                        "
                    >
                        <ResortPhoto
                            v-if="image('mark-green')"
                            :photo="image('mark-green')!"
                            :fill="false"
                            style="width: 52px"
                        />
                        {{ text('2_section_0_div_0_div_4_p_1') }}
                    </p>
                </div>
                <div class="stack">
                    <ResortPhoto
                        v-if="image('aerial')"
                        :photo="image('aerial')!"
                        :fill="false"
                        class="s1"
                        sizes="(max-width: 900px) 92vw, 45vw"
                    />
                    <ResortPhoto
                        v-if="image('sun-villa')"
                        :photo="image('sun-villa')!"
                        :fill="false"
                        class="s2"
                        sizes="(max-width: 900px) 50vw, 24vw"
                    />
                    <div class="tag">
                        <b>
                            {{ text('2_section_0_div_1_div_2_div_0_b_0') }}
                        </b>
                        {{ text('2_section_0_div_1_div_2_div_1') }}
                    </div>
                </div>
            </div>
        </section>
        <section class="sand">
            <div class="wrap">
                <div class="sec-head">
                    <div>
                        <span class="kicker">
                            {{ text('3_section_0_div_0_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('3_section_0_div_0_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('3_section_0_div_0_div_0_div_2_p_0') }}
                        </p>
                    </div>
                </div>
                <div class="panels">
                    <div
                        :class="{ on: activePanel === 0 }"
                        @click="activePanel = 0"
                        @mouseenter="activatePanel(0)"
                        class="panel"
                    >
                        <ResortPhoto
                            v-if="image('dusk-palms')"
                            :photo="image('dusk-palms')!"
                            :fill="false"
                            sizes="(max-width: 860px) 92vw, 60vw"
                        />
                        <div class="in">
                            <span class="num">
                                {{
                                    text(
                                        '3_section_0_div_1_div_0_div_1_div_0_span_0',
                                    )
                                }}
                            </span>
                            <h3>
                                <button
                                    type="button"
                                    :aria-expanded="activePanel === 0"
                                    aria-controls="audience-content-0"
                                    @click="activePanel = 0"
                                >
                                    {{
                                        text(
                                            '3_section_0_div_1_div_0_div_1_div_1_h3_0',
                                        )
                                    }}
                                </button>
                            </h3>
                            <div
                                class="more"
                                id="audience-content-0"
                                :inert="activePanel !== 0"
                            >
                                <ul>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_0_div_1_div_2_div_0_ul_0_li_0',
                                            )
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_0_div_1_div_2_div_0_ul_1_li_0',
                                            )
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_0_div_1_div_2_div_0_ul_2_li_0',
                                            )
                                        }}
                                    </li>
                                </ul>
                                <Link
                                    v-if="hasPage('villas.html')"
                                    :href="link('villas.html')"
                                    class="btn btn-brass"
                                >
                                    {{
                                        text(
                                            '3_section_0_div_1_div_0_div_1_div_2_div_1_a_0',
                                        )
                                    }}
                                    <svg>
                                        <use href="#i-arrow" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div
                        :class="{ on: activePanel === 1 }"
                        @click="activePanel = 1"
                        @mouseenter="activatePanel(1)"
                        class="panel"
                    >
                        <ResortPhoto
                            v-if="image('caravan')"
                            :photo="image('caravan')!"
                            :fill="false"
                            sizes="(max-width: 860px) 92vw, 60vw"
                        />
                        <div class="in">
                            <span class="num">
                                {{
                                    text(
                                        '3_section_0_div_1_div_1_div_1_div_0_span_0',
                                    )
                                }}
                            </span>
                            <h3>
                                <button
                                    type="button"
                                    :aria-expanded="activePanel === 1"
                                    aria-controls="audience-content-1"
                                    @click="activePanel = 1"
                                >
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_1_div_1_h3_0',
                                        )
                                    }}
                                </button>
                            </h3>
                            <div
                                class="more"
                                id="audience-content-1"
                                :inert="activePanel !== 1"
                            >
                                <ul>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_1_div_1_div_2_div_0_ul_0_li_0',
                                            )
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_1_div_1_div_2_div_0_ul_1_li_0',
                                            )
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_1_div_1_div_2_div_0_ul_2_li_0',
                                            )
                                        }}
                                    </li>
                                </ul>
                                <Link
                                    v-if="hasPage('experiences.html')"
                                    :href="link('experiences.html')"
                                    class="btn btn-brass"
                                >
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_1_div_2_div_1_a_0',
                                        )
                                    }}
                                    <svg>
                                        <use href="#i-arrow" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                    <div
                        :class="{ on: activePanel === 2 }"
                        @click="activePanel = 2"
                        @mouseenter="activatePanel(2)"
                        class="panel"
                    >
                        <ResortPhoto
                            v-if="image('olympic-lanes')"
                            :photo="image('olympic-lanes')!"
                            :fill="false"
                            sizes="(max-width: 860px) 92vw, 60vw"
                        />
                        <div class="in">
                            <span class="num">
                                {{
                                    text(
                                        '3_section_0_div_1_div_2_div_1_div_0_span_0',
                                    )
                                }}
                            </span>
                            <h3>
                                <button
                                    type="button"
                                    :aria-expanded="activePanel === 2"
                                    aria-controls="audience-content-2"
                                    @click="activePanel = 2"
                                >
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_1_div_1_h3_0',
                                        )
                                    }}
                                </button>
                            </h3>
                            <div
                                class="more"
                                id="audience-content-2"
                                :inert="activePanel !== 2"
                            >
                                <ul>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_2_div_1_div_2_div_0_ul_0_li_0',
                                            )
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_2_div_1_div_2_div_0_ul_1_li_0',
                                            )
                                        }}
                                    </li>
                                    <li>
                                        {{
                                            text(
                                                '3_section_0_div_1_div_2_div_1_div_2_div_0_ul_2_li_0',
                                            )
                                        }}
                                    </li>
                                </ul>
                                <Link
                                    v-if="hasPage('sport.html')"
                                    :href="link('sport.html')"
                                    class="btn btn-brass"
                                >
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_1_div_2_div_1_a_0',
                                        )
                                    }}
                                    <svg>
                                        <use href="#i-arrow" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="wrap">
                <div class="sec-head">
                    <div>
                        <span class="kicker">
                            {{ text('4_section_0_div_0_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('4_section_0_div_0_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('4_section_0_div_0_div_0_div_2_p_0') }}
                        </p>
                    </div>
                    <Link
                        v-if="hasPage('villas.html')"
                        :href="link('villas.html')"
                        class="btn btn-ghost"
                    >
                        {{ text('4_section_0_div_0_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
                </div>
                <RedesignStayCards :items="featuredStays" />
            </div>
        </section>
        <section class="band" aria-labelledby="band-h" style="--ratio: 1.7778">
            <ResortPhoto
                v-if="image('villa-pool')"
                :photo="image('villa-pool')!"
                :fill="false"
                sizes="100vw"
            />
            <div class="wrap">
                <div class="card-txt">
                    <span class="kicker">
                        {{ text('5_section_1_div_0_div_0_span_0') }}
                    </span>
                    <h2 id="band-h">
                        {{ text('5_section_1_div_0_div_1_h2_0') }}
                    </h2>
                    <p>
                        {{ text('5_section_1_div_0_div_2_p_0') }}
                    </p>
                    <Link
                        v-if="hasPage('villas.html')"
                        :href="link('villas.html')"
                        class="btn btn-ghost"
                        style="justify-self: start"
                    >
                        {{ text('5_section_1_div_0_div_3_a_0') }}
                    </Link>
                </div>
            </div>
        </section>
        <section class="sand" style="overflow: hidden">
            <div class="wrap">
                <div class="sec-head">
                    <div>
                        <span class="kicker">
                            {{ text('6_section_0_div_0_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('6_section_0_div_0_div_0_div_1_h2_0') }}
                        </h2>
                        <p class="lede">
                            {{ text('6_section_0_div_0_div_0_div_2_p_0') }}
                        </p>
                    </div>
                    <div class="ctrl">
                        <button
                            :aria-label="
                                text(
                                    '6_section_0_div_0_div_1_div_0_button_attr_aria_label',
                                )
                            "
                            @click="scrollCarousel(-1)"
                            :disabled="carouselAtStart"
                        >
                            <svg>
                                <use href="#i-left" />
                            </svg>
                        </button>
                        <button
                            :aria-label="
                                text(
                                    '6_section_0_div_0_div_1_div_1_button_attr_aria_label',
                                )
                            "
                            @click="scrollCarousel(1)"
                            :disabled="carouselAtEnd"
                        >
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="track" id="track" @scroll="updateCarousel">
                <article v-show="matches('desert')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-safari')"
                            :photo="image('xp-safari')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_0_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_0_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_0_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('desert culture')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-camel')"
                            :photo="image('xp-camel')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_1_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_1_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_1_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('culture')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-tent')"
                            :photo="image('xp-tent')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_2_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_2_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_2_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('desert')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-sandboard')"
                            :photo="image('xp-sandboard')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_3_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_3_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_3_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('desert')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-quad')"
                            :photo="image('xp-quad')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_4_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_4_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_4_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('desert')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-stars')"
                            :photo="image('xp-stars')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_5_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_5_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_5_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('culture')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-chef')"
                            :photo="image('xp-chef')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_6_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_6_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_6_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('trip')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-wadi')"
                            :photo="image('xp-wadi')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_7_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_7_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_7_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('trip')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('xp-turtle')"
                            :photo="image('xp-turtle')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_8_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_8_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_8_article_2_p_0') }}
                    </p>
                </article>
                <article v-show="matches('trip')" class="xc">
                    <div class="im">
                        <ResortPhoto
                            v-if="image('masirah')"
                            :photo="image('masirah')!"
                            :fill="false"
                            sizes="(max-width: 560px) 92vw, (max-width: 980px) 46vw, 30vw"
                        />
                        <span class="when">
                            {{
                                text('6_section_1_div_9_article_0_div_1_span_0')
                            }}
                        </span>
                    </div>
                    <h3>
                        {{ text('6_section_1_div_9_article_1_h3_0') }}
                    </h3>
                    <p>
                        {{ text('6_section_1_div_9_article_2_p_0') }}
                    </p>
                </article>
            </div>
            <div class="wrap" style="margin-top: 36px">
                <Link
                    v-if="hasPage('experiences.html')"
                    :href="link('experiences.html')"
                    class="btn btn-ghost"
                >
                    {{ text('6_section_2_div_0_a_0') }}
                    <svg>
                        <use href="#i-arrow" />
                    </svg>
                </Link>
            </div>
        </section>
        <section>
            <div class="wrap split">
                <div class="txt">
                    <span class="kicker">
                        {{ text('7_section_0_div_0_div_0_span_0') }}
                    </span>
                    <h2>
                        {{ text('7_section_0_div_0_div_1_h2_0') }}
                    </h2>
                    <p class="lede">
                        {{ text('7_section_0_div_0_div_2_p_0') }}
                    </p>
                    <Link
                        v-if="hasPage('dining.html')"
                        :href="link('dining.html')"
                        class="btn btn-ghost"
                        style="justify-self: start"
                    >
                        {{ text('7_section_0_div_0_div_3_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
                </div>
                <figure>
                    <ResortPhoto
                        v-if="image('dine-hall')"
                        :photo="image('dine-hall')!"
                        :fill="false"
                        sizes="(max-width: 900px) 92vw, 45vw"
                    />
                </figure>
            </div>
        </section>
        <section class="dark on-dark">
            <div class="wrap split">
                <div class="txt">
                    <span class="kicker">
                        {{ text('8_section_0_div_0_div_0_span_0') }}
                    </span>
                    <h2>
                        {{ text('8_section_0_div_0_div_1_h2_0') }}
                    </h2>
                    <p class="lede">
                        {{ text('8_section_0_div_0_div_2_p_0') }}
                    </p>
                    <Link
                        v-if="hasPage('sport.html')"
                        :href="link('sport.html')"
                        class="btn btn-ghost"
                        style="justify-self: start"
                    >
                        {{ text('8_section_0_div_0_div_3_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
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
        </section>
        <section>
            <div class="wrap">
                <div class="sec-head">
                    <div>
                        <span class="kicker">
                            {{ text('9_section_0_div_0_div_0_div_0_span_0') }}
                        </span>
                        <h2>
                            {{ text('9_section_0_div_0_div_0_div_1_h2_0') }}
                        </h2>
                    </div>
                    <Link
                        v-if="hasPage('gallery.html')"
                        :href="link('gallery.html')"
                        class="btn btn-ghost"
                    >
                        {{ text('9_section_0_div_0_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
                </div>
                <div class="mosaic">
                    <button @click="viewPhoto('hero', galleryKeys)" class="g1">
                        <ResortPhoto
                            v-if="image('hero')"
                            :photo="image('hero')!"
                            :fill="false"
                        />
                    </button>
                    <button
                        @click="viewPhoto('sun-villa', galleryKeys)"
                        class="g2"
                    >
                        <ResortPhoto
                            v-if="image('sun-villa')"
                            :photo="image('sun-villa')!"
                            :fill="false"
                        />
                    </button>
                    <button
                        @click="viewPhoto('stadium', galleryKeys)"
                        class="g3"
                    >
                        <ResortPhoto
                            v-if="image('stadium')"
                            :photo="image('stadium')!"
                            :fill="false"
                        />
                    </button>
                    <button
                        @click="viewPhoto('dusk-tree', galleryKeys)"
                        class="g4"
                    >
                        <ResortPhoto
                            v-if="image('dusk-tree')"
                            :photo="image('dusk-tree')!"
                            :fill="false"
                        />
                    </button>
                    <button
                        @click="viewPhoto('playground', galleryKeys)"
                        class="g5"
                    >
                        <ResortPhoto
                            v-if="image('playground')"
                            :photo="image('playground')!"
                            :fill="false"
                        />
                    </button>
                    <button
                        @click="viewPhoto('bridge', galleryKeys)"
                        class="g6"
                    >
                        <ResortPhoto
                            v-if="image('bridge')"
                            :photo="image('bridge')!"
                            :fill="false"
                        />
                    </button>
                </div>
            </div>
        </section>
        <section class="sand">
            <div class="wrap split" style="align-items: center">
                <div class="txt">
                    <span class="kicker">
                        {{ text('10_section_0_div_0_div_0_span_0') }}
                    </span>
                    <h2>
                        {{ text('10_section_0_div_0_div_1_h2_0') }}
                    </h2>
                    <p class="lede">
                        {{ text('10_section_0_div_0_div_2_p_0') }}
                    </p>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 12px">
                    <Link
                        v-if="hasPage('sport.html#camps')"
                        :href="link('sport.html#camps')"
                        class="btn btn-ghost"
                    >
                        {{ text('10_section_0_div_1_div_0_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
                    <Link
                        v-if="hasPage('school-trips.html')"
                        :href="link('school-trips.html')"
                        class="btn btn-ghost"
                    >
                        {{ text('10_section_0_div_1_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
                    <button
                        @click="openBooking('group')"
                        class="btn btn-palm"
                        type="button"
                    >
                        {{ text('10_section_0_div_1_div_2_a_0') }}
                    </button>
                </div>
            </div>
        </section>
        <section class="sand">
            <div class="wrap cta">
                <span class="kicker">
                    {{ text('11_section_0_div_0_span_0') }}
                </span>
                <h2>
                    {{ text('11_section_0_div_1_h2_0') }}
                </h2>
                <p class="lede">
                    {{ text('11_section_0_div_2_p_0') }}
                </p>
                <div class="row">
                    <button
                        @click="openBooking('stay')"
                        class="btn btn-palm"
                        type="button"
                    >
                        {{ text('11_section_0_div_3_div_0_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </button>
                    <Link
                        v-if="hasPage('location.html')"
                        :href="link('location.html')"
                        class="btn btn-ghost"
                    >
                        {{ text('11_section_0_div_3_div_1_a_0') }}
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
