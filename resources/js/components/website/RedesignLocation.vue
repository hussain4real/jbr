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
} = useRedesign('contact');
</script>
<template>
    <div>
        <section class="hero" id="top-hero">
            <div class="hero-img" style="--ratio: 1.5009">
                <ResortPhoto
                    v-if="image('aerial')"
                    :photo="image('aerial')!"
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
            <div class="wrap split">
                <div class="txt">
                    <p class="lede" style="white-space: pre-line">
                        {{ text('2_section_0_div_0_div_0_p_0') }}
                    </p>
                    <a
                        :href="profile.map_url ?? undefined"
                        v-if="profile.map_url"
                        class="btn btn-ghost"
                        style="justify-self: start"
                        target="_blank"
                        rel="noopener"
                    >
                        {{ text('2_section_0_div_0_div_1_a_0') }}
                        <svg>
                            <use href="#i-arrow" />
                        </svg>
                    </a>
                </div>
                <div class="route">
                    <p class="rh">
                        {{ text('2_section_0_div_1_div_0_p_0') }}
                    </p>
                    <ol>
                        <li>
                            <span class="dot"> </span>
                            <div>
                                <b>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_0_li_1_div_0_b_0',
                                        )
                                    }}
                                </b>
                                <small>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_0_li_1_div_1_small_0',
                                        )
                                    }}
                                </small>
                            </div>
                            <span class="t">
                                {{
                                    text(
                                        '2_section_0_div_1_div_1_ol_0_li_2_span_0',
                                    )
                                }}
                            </span>
                        </li>
                        <li>
                            <span class="dot"> </span>
                            <div>
                                <b>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_1_li_1_div_0_b_0',
                                        )
                                    }}
                                </b>
                                <small>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_1_li_1_div_1_small_0',
                                        )
                                    }}
                                </small>
                            </div>
                            <span class="t">
                                {{
                                    text(
                                        '2_section_0_div_1_div_1_ol_1_li_2_span_0',
                                    )
                                }}
                            </span>
                        </li>
                        <li class="home">
                            <span class="dot"> </span>
                            <div>
                                <b>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_2_li_1_div_0_b_0',
                                        )
                                    }}
                                </b>
                                <small>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_2_li_1_div_1_small_0',
                                        )
                                    }}
                                </small>
                            </div>
                            <span class="t">
                                {{
                                    text(
                                        '2_section_0_div_1_div_1_ol_2_li_2_span_0',
                                    )
                                }}
                            </span>
                        </li>
                        <li>
                            <span class="dot"> </span>
                            <div>
                                <b>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_3_li_1_div_0_b_0',
                                        )
                                    }}
                                </b>
                                <small>
                                    {{
                                        text(
                                            '2_section_0_div_1_div_1_ol_3_li_1_div_1_small_0',
                                        )
                                    }}
                                </small>
                            </div>
                            <span class="t">
                                {{
                                    text(
                                        '2_section_0_div_1_div_1_ol_3_li_2_span_0',
                                    )
                                }}
                            </span>
                        </li>
                    </ol>
                    <p class="note2">
                        {{ text('2_section_0_div_1_div_2_p_0') }}
                    </p>
                </div>
            </div>
        </section>
        <section class="night on-dark" id="enquire">
            <div class="wrap contact-grid">
                <div class="txt">
                    <span class="kicker">
                        {{ text('3_section_0_div_0_div_0_span_0') }}
                    </span>
                    <h2>
                        {{ text('3_section_0_div_0_div_1_h2_0') }}
                    </h2>
                    <p class="lede">
                        {{ text('3_section_0_div_0_div_2_p_0') }}
                    </p>
                    <dl class="lines">
                        <div>
                            <dt>
                                {{
                                    profile.whatsapp
                                        ? text(
                                              '3_section_0_div_0_div_3_dl_0_div_0_dt_0',
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
                                        '3_section_0_div_0_div_3_dl_0_div_2_button_0',
                                    )
                                }}
                            </button>
                        </div>
                        <div>
                            <dt>
                                {{
                                    text(
                                        '3_section_0_div_0_div_3_dl_1_div_0_dt_0',
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
                                        '3_section_0_div_0_div_3_dl_1_div_2_button_0',
                                    )
                                }}
                            </button>
                        </div>
                        <div>
                            <dt>
                                {{
                                    text(
                                        '3_section_0_div_0_div_3_dl_2_div_0_dt_0',
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
                                        '3_section_0_div_0_div_3_dl_2_div_2_a_0',
                                    )
                                }}
                            </a>
                        </div>
                        <div>
                            <dt>
                                {{
                                    text(
                                        '3_section_0_div_0_div_3_dl_3_div_0_dt_0',
                                    )
                                }}
                            </dt>
                            <dd dir="ltr" style="text-align: start">
                                {{
                                    text(
                                        '3_section_0_div_0_div_3_dl_3_div_1_dd_0',
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
                                        '3_section_0_div_0_div_3_dl_3_div_2_a_0',
                                    )
                                }}
                            </a>
                        </div>
                    </dl>
                    <p v-if="copyStatus" role="status">{{ copyStatus }}</p>
                </div>
                <RedesignEnquiry
                    :audience="'stay'"
                    :heading="text('3_section_0_div_1_form_0_h3_0')"
                />
            </div>
        </section>
    </div>
</template>
