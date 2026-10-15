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
} = useRedesign('experiences');
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 1.8605">
                <ResortPhoto
                    v-if="image('dunes')"
                    :photo="image('dunes')!"
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
                        @click="setFilter('desert')"
                        :aria-pressed="activeFilter === 'desert'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_1_button_0') }}
                    </button>
                    <button
                        @click="setFilter('culture')"
                        :aria-pressed="activeFilter === 'culture'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_2_button_0') }}
                    </button>
                    <button
                        @click="setFilter('trip')"
                        :aria-pressed="activeFilter === 'trip'"
                        type="button"
                    >
                        {{ text('2_section_0_div_1_div_3_button_0') }}
                    </button>
                </div>
                <div class="xgrid" id="xgrid">
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
                                    text(
                                        '2_section_0_div_2_div_0_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_0_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_0_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_0_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_0_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_1_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_1_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_1_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_1_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_1_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_2_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_2_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_2_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_2_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_2_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_3_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_3_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_3_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_3_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_3_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_4_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_4_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_4_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_4_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_4_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_5_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_5_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_5_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_5_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_5_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_6_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_6_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_6_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_6_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_6_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_7_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_7_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_7_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_7_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_7_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_8_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_8_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_8_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_8_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_8_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
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
                                    text(
                                        '2_section_0_div_2_div_9_article_0_div_1_span_0',
                                    )
                                }}
                            </span>
                        </div>
                        <h3>
                            {{ text('2_section_0_div_2_div_9_article_1_h3_0') }}
                        </h3>
                        <p>
                            {{ text('2_section_0_div_2_div_9_article_2_p_0') }}
                        </p>
                        <button
                            @click="
                                enquire(
                                    text(
                                        '2_section_0_div_2_div_9_article_3_a_attr_data_enquire',
                                    ),
                                )
                            "
                            class="link"
                            type="button"
                        >
                            {{ text('2_section_0_div_2_div_9_article_3_a_0') }}
                            <svg>
                                <use href="#i-arrow" />
                            </svg>
                        </button>
                    </article>
                </div>
                <div class="note">
                    <span>
                        {{ text('2_section_0_div_3_div_0_span_0') }}
                    </span>
                    <Link :href="link('location.html')" class="btn btn-palm">
                        {{ text('2_section_0_div_3_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </Link>
                </div>
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
