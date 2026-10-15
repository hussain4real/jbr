<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import RequestForm from '@/components/website/RequestForm.vue';
import { useRedesign } from '@/composables/useRedesign';
import type { RequestDetails } from '@/types/website';
const component = defineProps<{
    audience?: string;
    modal?: boolean;
    heading?: string;
}>();
const { props: website, draft, label, whatsappUrl } = useRedesign();
const audience = ref(
    component.audience === 'schools' ? 'school' : draft.audience,
);
const selectedExperiences = ref<string[]>([]);
const directError = ref('');
const copied = ref(false);
const liveDetails = ref<RequestDetails | null>(null);
watch(
    () => draft.audience,
    (value) => {
        audience.value = value;
    },
);
const experiences = computed(() =>
    Object.entries(website.redesign.pages.experiences?.copy ?? {})
        .filter(([key]) => key.endsWith('_h3_0'))
        .map(([key, title]) => ({ key, title })),
);
const booking = computed(
    () => component.modal && draft.mode === 'stay' && !draft.experience,
);
const arrival = computed(() =>
    booking.value
        ? (liveDetails.value?.arrival ?? draft.arrival)
        : draft.arrival,
);
const departure = computed(() =>
    booking.value
        ? (liveDetails.value?.departure ?? draft.departure)
        : draft.departure,
);
const accommodation = computed(() =>
    audience.value !== 'stay'
        ? ''
        : booking.value
          ? String(liveDetails.value?.accommodation_id ?? draft.accommodation)
          : draft.accommodation,
);
const audiences = computed<Record<string, string>>(() => ({
    stay: label('A stay', 'إقامة'),
    school: label('A school or university trip', 'رحلة مدرسية أو جامعية'),
    team: label('A sports team or training camp', 'فريق أو معسكر رياضي'),
    group: label('Family or friends', 'العائلة أو الأصدقاء'),
    other: label('Something else', 'شيء آخر'),
}));
const partyLabel = computed(
    () =>
        (
            ({
                '1 adult': label('1 adult', 'شخص بالغ'),
                '2 adults': label('2 adults', 'شخصان بالغان'),
                '2 adults, 1 child': label('2 adults, 1 child', 'بالغان وطفل'),
                '2 adults, 2 children': label(
                    '2 adults, 2 children',
                    'بالغان وطفلان',
                ),
                '2 adults, 3+ children': label(
                    '2 adults, 3+ children',
                    'بالغان و٣ أطفال أو أكثر',
                ),
                'Group of friends': label('Group of friends', 'مجموعة أصدقاء'),
            }) as Record<string, string>
        )[draft.party] ?? draft.party,
);
const experienceTitle = computed(
    () =>
        website.redesign.pages.experiences?.copy[draft.experience] ??
        draft.experience,
);
const partySummary = computed(() => {
    if (
        !booking.value ||
        !liveDetails.value ||
        String(liveDetails.value.guests) === String(initial.value.guests)
    )
        return partyLabel.value;
    return liveDetails.value.guests === ''
        ? label('Not specified', 'غير محدد')
        : String(liveDetails.value.guests) + ' ' + label('guests', 'ضيوف');
});
const context = computed(() =>
    [
        label('I am planning', 'أخطط لـ') +
            ': ' +
            (audiences.value[audience.value] ?? audiences.value.stay),
        arrival.value ? label('Arrive', 'الوصول') + ': ' + arrival.value : '',
        departure.value
            ? label('Depart', 'المغادرة') + ': ' + departure.value
            : '',
        component.modal && draft.party
            ? label('Guests / group size', 'الضيوف / حجم المجموعة') +
              ': ' +
              partySummary.value
            : '',
        accommodation.value
            ? label('Villa', 'الفيلا') +
              ': ' +
              (website.accommodations.find(
                  (stay) => String(stay.id) === accommodation.value,
              )?.title ?? '')
            : '',
        draft.experience || selectedExperiences.value.length
            ? label(
                  'Experiences (priced separately)',
                  'التجارب (بتكلفة منفصلة)',
              ) +
              ': ' +
              [
                  experienceTitle.value,
                  ...selectedExperiences.value.map(
                      (key) =>
                          experiences.value.find((xp) => xp.key === key)?.title,
                  ),
              ]
                  .filter(Boolean)
                  .join('، ')
            : '',
    ]
        .filter(Boolean)
        .join('\n'),
);
const initial = computed(() => ({
    arrival: draft.arrival,
    departure: draft.departure,
    accommodation_id: draft.accommodation,
    guests:
        (
            {
                '1 adult': 1,
                '2 adults': 2,
                '2 adults, 1 child': 3,
                '2 adults, 2 children': 4,
            } as Record<string, number>
        )[draft.party] ?? '',
}));
watch(context, () => {
    directError.value = '';
    copied.value = false;
});
function updateDetails(details: RequestDetails): void {
    liveDetails.value = details;
    if (booking.value) draft.accommodation = String(details.accommodation_id);
}
interface Contacts {
    name: string;
    phone: string;
    message: string;
}
function normalizedPhone(value: string): string {
    return value
        .replace(/[٠-٩۰-۹]/g, (digit) =>
            String(
                '٠١٢٣٤٥٦٧٨٩'.includes(digit)
                    ? '٠١٢٣٤٥٦٧٨٩'.indexOf(digit)
                    : '۰۱۲۳۴۵۶۷۸۹'.indexOf(digit),
            ),
        )
        .replace(/[\s().-]/g, '');
}
function message(data: Contacts): string {
    return [
        label('Hello Jawharat Bidiyah Resort,', 'مرحباً منتجع جوهرة بدية،'),
        context.value,
        label('Name', 'الاسم') + ': ' + data.name.trim(),
        label('Phone', 'الهاتف') + ': ' + normalizedPhone(data.phone),
        data.message,
        label('Thank you.', 'شكراً.'),
    ]
        .filter(Boolean)
        .join('\n\n');
}
function validate(data: Contacts, event?: Event): boolean {
    if (
        !data.name.trim() ||
        !/^\+[1-9][0-9]{7,14}$/.test(normalizedPhone(data.phone))
    ) {
        event?.preventDefault();
        directError.value = label(
            'Add your name and an international phone number starting with +.',
            'أضف اسمك ورقم هاتف دولياً يبدأ بعلامة +.',
        );
        return false;
    }
    if (
        (arrival.value || departure.value) &&
        (!arrival.value ||
            !departure.value ||
            arrival.value < website.today ||
            departure.value <= arrival.value)
    ) {
        event?.preventDefault();
        directError.value = label(
            'Choose valid arrival and departure dates, or leave both empty.',
            'اختر تاريخي وصول ومغادرة صحيحين أو اتركهما فارغين.',
        );
        return false;
    }
    directError.value = '';
    return true;
}
async function copy(data: Contacts): Promise<void> {
    if (!validate(data)) return;
    try {
        await navigator.clipboard.writeText(message(data));
        copied.value = true;
    } catch {
        directError.value = label(
            'Copy is unavailable. Use WhatsApp or email instead.',
            'تعذر النسخ. استخدم واتساب أو البريد الإلكتروني.',
        );
    }
}
</script>
<template>
    <div class="v2-enquiry" :class="{ 'v2-enquiry-card': !modal }">
        <h3 v-if="heading">{{ heading }}</h3>
        <div v-if="!modal" class="enquiry-context">
            <div class="fld">
                <label for="enquiry-audience">{{
                    label('I am planning', 'أخطط لـ')
                }}</label
                ><select id="enquiry-audience" v-model="audience">
                    <option
                        v-for="(name, key) in audiences"
                        :key="key"
                        :value="key"
                    >
                        {{ name }}
                    </option>
                </select>
            </div>
            <div class="fld">
                <label for="enquiry-arrival">{{
                    label('Arrive (optional)', 'الوصول (اختياري)')
                }}</label
                ><input
                    id="enquiry-arrival"
                    v-model="draft.arrival"
                    type="date"
                    :min="website.today"
                />
            </div>
            <div class="fld">
                <label for="enquiry-departure">{{
                    label('Depart (optional)', 'المغادرة (اختياري)')
                }}</label
                ><input
                    id="enquiry-departure"
                    v-model="draft.departure"
                    type="date"
                    :min="draft.arrival || website.today"
                />
            </div>
            <fieldset v-if="experiences.length" class="experience-choices">
                <legend>
                    {{
                        label(
                            'Experiences you would like us to arrange (priced separately)',
                            'تجارب ترغب أن نرتبها لك (بتكلفة منفصلة)',
                        )
                    }}
                </legend>
                <label v-for="experience in experiences" :key="experience.key"
                    ><input
                        v-model="selectedExperiences"
                        type="checkbox"
                        :value="experience.key"
                    />
                    {{ experience.title }}</label
                >
            </fieldset>
        </div>
        <p class="enquiry-summary" style="white-space: pre-line">
            {{ context }}
        </p>
        <RequestForm
            :key="booking ? 'booking' : 'enquiry'"
            :locale="website.locale"
            :copy="website.copy"
            :booking="!!booking"
            :accommodations="website.accommodations"
            :token="
                booking
                    ? website.submissionTokens.booking
                    : website.submissionTokens.enquiry
            "
            :selected="draft.accommodation || null"
            :today="website.today"
            :enabled="website.acceptsRequests"
            :has-contacts="!!(website.profile.phone || website.profile.email)"
            :has-privacy="!!website.profile.privacy"
            :initial="initial"
            :context="context"
            :allow-direct="!!(whatsappUrl || website.profile.email)"
            @update:details="updateDetails"
        >
            <template #direct="{ data }">
                <div class="direct-actions">
                    <p v-if="directError" role="alert">{{ directError }}</p>
                    <a
                        v-if="whatsappUrl"
                        class="btn btn-palm"
                        :href="
                            whatsappUrl +
                            '?text=' +
                            encodeURIComponent(message(data))
                        "
                        target="_blank"
                        rel="noopener noreferrer"
                        @click="validate(data, $event)"
                        >{{ label('Send on WhatsApp', 'إرسال عبر واتساب') }}</a
                    >
                    <button
                        type="button"
                        class="btn btn-ghost"
                        @click="copy(data)"
                    >
                        {{
                            copied
                                ? label('Copied', 'تم النسخ')
                                : label('Copy message', 'نسخ الرسالة')
                        }}
                    </button>
                    <a
                        v-if="website.profile.email"
                        class="link"
                        :href="
                            'mailto:' +
                            website.profile.email +
                            '?subject=' +
                            encodeURIComponent(
                                label('Resort enquiry', 'استفسار عن المنتجع'),
                            ) +
                            '&body=' +
                            encodeURIComponent(message(data))
                        "
                        @click="validate(data, $event)"
                        >{{
                            label('Send by email', 'إرسال بالبريد الإلكتروني')
                        }}</a
                    >
                    <p>
                        {{
                            label(
                                'These actions open your messaging app. Your message is sent only when you send it there.',
                                'تفتح هذه الإجراءات تطبيق المراسلة. تُرسل الرسالة فقط عندما ترسلها من هناك.',
                            )
                        }}
                    </p>
                </div>
            </template>
        </RequestForm>
    </div>
</template>
