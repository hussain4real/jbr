<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import RedesignBookingPanel from '@/components/website/RedesignBookingPanel.vue';
import RedesignEnquiry from '@/components/website/RedesignEnquiry.vue';
import RedesignStayCards from '@/components/website/RedesignStayCards.vue';
import { useRedesign } from '@/composables/useRedesign';
const {
    text,
    label,
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
    copyStatus,
    activePanel,
    activatePanel,
    scrollCarousel,
} = useRedesign('schools');
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 1.6675">
                <ResortPhoto
                    v-if="image('grounds')"
                    :photo="image('grounds')!"
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
                    <li>
                        <span aria-current="page">
                            {{ text('1_nav_0_div_0_ol_2_li_0_span_0') }}
                        </span>
                    </li>
                </ol>
            </div>
        </nav>
        <section>
            <div class="wrap split">
                <div class="txt">
                    <p class="lede">
                        {{ text('2_section_0_div_0_div_0_p_0') }}
                    </p>
                    <a
                        class="btn btn-palm"
                        href="#enquire"
                        style="justify-self: start"
                    >
                        {{ text('2_section_0_div_0_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </a>
                </div>
                <figure>
                    <ResortPhoto
                        v-if="image('quad')"
                        :photo="image('quad')!"
                        :fill="false"
                        sizes="(max-width: 900px) 92vw, 45vw"
                    />
                </figure>
            </div>
            <div class="wrap" style="margin-top: clamp(56px, 7vw, 96px)">
                <ol class="steps">
                    <li>
                        <span class="n">
                            {{ text('2_section_1_div_0_ol_0_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('2_section_1_div_0_ol_0_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('2_section_1_div_0_ol_0_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <span class="n">
                            {{ text('2_section_1_div_0_ol_1_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('2_section_1_div_0_ol_1_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('2_section_1_div_0_ol_1_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <span class="n">
                            {{ text('2_section_1_div_0_ol_2_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('2_section_1_div_0_ol_2_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('2_section_1_div_0_ol_2_li_2_span_0') }}
                        </span>
                    </li>
                    <li>
                        <span class="n">
                            {{ text('2_section_1_div_0_ol_3_li_0_span_0') }}
                        </span>
                        <b>
                            {{ text('2_section_1_div_0_ol_3_li_1_b_0') }}
                        </b>
                        <span>
                            {{ text('2_section_1_div_0_ol_3_li_2_span_0') }}
                        </span>
                    </li>
                </ol>
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
                <div class="days">
                    <div class="day">
                        <span class="d">
                            {{ text('3_section_0_div_1_div_0_div_0_span_0') }}
                        </span>
                        <h3>
                            {{ text('3_section_0_div_1_div_0_div_1_h3_0') }}
                        </h3>
                        <ul>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_0_div_2_ul_0_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_0_div_2_ul_0_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_0_div_2_ul_1_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_0_div_2_ul_1_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="day">
                        <span class="d">
                            {{ text('3_section_0_div_1_div_1_div_0_span_0') }}
                        </span>
                        <h3>
                            {{ text('3_section_0_div_1_div_1_div_1_h3_0') }}
                        </h3>
                        <ul>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_2_ul_0_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_2_ul_0_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_2_ul_1_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_2_ul_1_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_2_ul_2_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_1_div_2_ul_2_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="day">
                        <span class="d">
                            {{ text('3_section_0_div_1_div_2_div_0_span_0') }}
                        </span>
                        <h3>
                            {{ text('3_section_0_div_1_div_2_div_1_h3_0') }}
                        </h3>
                        <ul>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_2_ul_0_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_2_ul_0_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_2_ul_1_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_2_ul_1_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                            <li>
                                <b>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_2_ul_2_li_0_b_0',
                                        )
                                    }}
                                </b>
                                <span>
                                    {{
                                        text(
                                            '3_section_0_div_1_div_2_div_2_ul_2_li_1_span_0',
                                        )
                                    }}
                                </span>
                            </li>
                        </ul>
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
                    </div>
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
        <section class="night on-dark" id="enquire">
            <div class="wrap contact-grid">
                <div class="txt">
                    <span class="kicker">
                        {{ text('5_section_0_div_0_div_0_span_0') }}
                    </span>
                    <h2>
                        {{ text('5_section_0_div_0_div_1_h2_0') }}
                    </h2>
                    <p class="lede">
                        {{ text('5_section_0_div_0_div_2_p_0') }}
                    </p>
                    <dl class="lines">
                        <div>
                            <dt>
                                {{
                                    profile.whatsapp
                                        ? text(
                                              '5_section_0_div_0_div_3_dl_0_div_0_dt_0',
                                          )
                                        : label('Phone', 'الهاتف')
                                }}
                            </dt>
                            <dd dir="ltr" style="text-align: start">
                                {{ profile.whatsapp || profile.phone }}
                            </dd>
                            <button
                                @click="
                                    copyContact(
                                        profile.whatsapp ? 'whatsapp' : 'phone',
                                    )
                                "
                                :disabled="!(profile.whatsapp || profile.phone)"
                                class="chip"
                            >
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_0_div_2_button_0',
                                    )
                                }}
                            </button>
                        </div>
                        <div>
                            <dt>
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_1_div_0_dt_0',
                                    )
                                }}
                            </dt>
                            <dd>
                                {{ profile.email }}
                            </dd>
                            <button
                                @click="copyContact('email')"
                                :disabled="!profile.email"
                                class="chip"
                            >
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_1_div_2_button_0',
                                    )
                                }}
                            </button>
                        </div>
                        <div>
                            <dt>
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_2_div_0_dt_0',
                                    )
                                }}
                            </dt>
                            <dd>
                                {{ profile.address }}
                            </dd>
                            <a
                                :href="profile.map_url ?? undefined"
                                v-if="profile.map_url"
                                class="chip"
                                target="_blank"
                                rel="noopener"
                            >
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_2_div_2_a_0',
                                    )
                                }}
                            </a>
                        </div>
                        <div>
                            <dt>
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_3_div_0_dt_0',
                                    )
                                }}
                            </dt>
                            <dd dir="ltr" style="text-align: start">
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_3_div_1_dd_0',
                                    )
                                }}
                            </dd>
                            <a
                                :href="profile.instagram_url ?? undefined"
                                v-if="profile.instagram_url"
                                class="chip"
                                target="_blank"
                                rel="noopener"
                            >
                                {{
                                    text(
                                        '5_section_0_div_0_div_3_dl_3_div_2_a_0',
                                    )
                                }}
                            </a>
                        </div>
                    </dl>
                    <p v-if="copyStatus" role="status">{{ copyStatus }}</p>
                </div>
                <RedesignEnquiry :audience="'schools'" />
            </div>
        </section>
    </div>
</template>
