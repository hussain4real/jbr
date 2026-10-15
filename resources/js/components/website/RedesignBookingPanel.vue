<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { useRedesign } from '@/composables/useRedesign';
const props = defineProps<{ inline?: boolean }>();
const {
    draft,
    label,
    profile,
    bookingOpen,
    enquiryOpen,
    props: website,
} = useRedesign();
const group = computed(() => draft.mode !== 'stay');
watch(
    group,
    (isGroup) => {
        const ranges = ['10–20', '21–40', '41–80', '80+'];
        if (isGroup && !ranges.includes(draft.party)) draft.party = '10–20';
        if (!isGroup && ranges.includes(draft.party)) draft.party = '2 adults';
    },
    { immediate: true },
);
const error = ref('');
function proceed(): void {
    if (
        !draft.arrival ||
        !draft.departure ||
        draft.arrival < website.today ||
        draft.departure <= draft.arrival
    ) {
        error.value = label(
            'Choose an arrival from today and a departure after arrival.',
            'اختر وصولاً من اليوم ومغادرة بعد تاريخ الوصول.',
        );
        return;
    }
    error.value = '';
    bookingOpen.value = false;
    enquiryOpen.value = true;
}
</script>
<template>
    <div :class="props.inline ? 'wrap booker' : ''">
        <div class="bk">
            <div
                class="bk-tabs"
                role="group"
                :aria-label="label('Type of stay', 'نوع الإقامة')"
            >
                <button
                    type="button"
                    class="bk-tab"
                    :aria-pressed="!group"
                    @click="
                        draft.mode = 'stay';
                        draft.audience = 'stay';
                    "
                >
                    <svg aria-hidden="true"><use href="#i-bed" /></svg>
                    {{ label('Holiday stay', 'إقامة سياحية') }}
                </button>
                <button
                    type="button"
                    class="bk-tab"
                    :aria-pressed="group"
                    @click="
                        draft.mode = 'group';
                        draft.audience = 'group';
                    "
                >
                    <svg aria-hidden="true"><use href="#i-team" /></svg>
                    {{ label('Groups', 'المجموعات') }}
                </button>
            </div>
            <form class="bk-form" novalidate @submit.prevent="proceed">
                <div class="fld">
                    <label :for="inline ? 'home-arrival' : 'panel-arrival'">{{
                        label('Arrive', 'الوصول')
                    }}</label
                    ><input
                        :id="inline ? 'home-arrival' : 'panel-arrival'"
                        v-model="draft.arrival"
                        type="date"
                        :min="website.today"
                        required
                    />
                </div>
                <div class="fld">
                    <label
                        :for="inline ? 'home-departure' : 'panel-departure'"
                        >{{ label('Depart', 'المغادرة') }}</label
                    ><input
                        :id="inline ? 'home-departure' : 'panel-departure'"
                        v-model="draft.departure"
                        type="date"
                        :min="draft.arrival || website.today"
                        required
                    />
                </div>
                <div class="fld">
                    <label :for="inline ? 'home-party' : 'panel-party'">{{
                        group
                            ? label('Group size', 'حجم المجموعة')
                            : label('Guests', 'الضيوف')
                    }}</label>
                    <select
                        :id="inline ? 'home-party' : 'panel-party'"
                        v-model="draft.party"
                    >
                        <template v-if="group"
                            ><option value="10–20">10–20</option>
                            <option value="21–40">21–40</option>
                            <option value="41–80">41–80</option>
                            <option value="80+">80+</option></template
                        >
                        <template v-else
                            ><option value="1 adult">
                                {{ label('1 adult', 'شخص بالغ') }}
                            </option>
                            <option value="2 adults">
                                {{ label('2 adults', 'شخصان بالغان') }}
                            </option>
                            <option value="2 adults, 1 child">
                                {{ label('2 adults, 1 child', 'بالغان وطفل') }}
                            </option>
                            <option value="2 adults, 2 children">
                                {{
                                    label(
                                        '2 adults, 2 children',
                                        'بالغان وطفلان',
                                    )
                                }}
                            </option>
                            <option value="2 adults, 3+ children">
                                {{
                                    label(
                                        '2 adults, 3+ children',
                                        'بالغان و٣ أطفال أو أكثر',
                                    )
                                }}
                            </option>
                            <option value="Group of friends">
                                {{ label('Group of friends', 'مجموعة أصدقاء') }}
                            </option></template
                        >
                    </select>
                </div>
                <div class="fld">
                    <label :for="inline ? 'home-choice' : 'panel-choice'">{{
                        group
                            ? label('Travelling as', 'نوع المجموعة')
                            : label('Villa', 'الفيلا')
                    }}</label>
                    <select
                        v-if="group"
                        :id="inline ? 'home-choice' : 'panel-choice'"
                        v-model="draft.audience"
                    >
                        <option value="group">
                            {{
                                label(
                                    'Family or friends',
                                    'العائلة أو الأصدقاء',
                                )
                            }}
                        </option>
                        <option value="team">
                            {{
                                label(
                                    'Sports team or camp',
                                    'فريق أو معسكر رياضي',
                                )
                            }}
                        </option>
                        <option value="school">
                            {{
                                label('School or university', 'مدرسة أو جامعة')
                            }}
                        </option>
                        <option value="other">
                            {{ label('Other group', 'مجموعة أخرى') }}
                        </option>
                    </select>
                    <select
                        v-else
                        :id="inline ? 'home-choice' : 'panel-choice'"
                        v-model="draft.accommodation"
                    >
                        <option value="">
                            {{ label('Any villa or room', 'أي فيلا أو غرفة') }}
                        </option>
                        <option
                            v-for="stay in website.accommodations"
                            :key="stay.id"
                            :value="String(stay.id)"
                        >
                            {{ stay.title }}
                        </option>
                    </select>
                </div>
                <div class="bk-go">
                    <button class="btn btn-palm" type="submit">
                        {{
                            group
                                ? label(
                                      'Request a group proposal',
                                      'اطلب عرضاً للمجموعة',
                                  )
                                : label(
                                      'Check availability',
                                      'استفسر عن التوفر',
                                  )
                        }}
                        <svg aria-hidden="true"><use href="#i-arrow" /></svg>
                    </button>
                </div>
            </form>
            <p v-if="error" role="alert" class="booking-error">{{ error }}</p>
            <div class="bk-foot">
                <span>{{
                    label(
                        'Our team confirms availability and rates after your enquiry.',
                        'يؤكد فريقنا التوفر والأسعار بعد استفسارك.',
                    )
                }}</span
                ><span v-if="profile.phone"
                    >{{ label('Prefer to talk?', 'تفضل التحدث؟') }}
                    <b dir="ltr">{{ profile.phone }}</b></span
                >
            </div>
        </div>
    </div>
</template>
