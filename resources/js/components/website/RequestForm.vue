<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { computed, nextTick, ref, useId, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    booking as sendBooking,
    enquiry as sendEnquiry,
} from '@/actions/App/Http/Controllers/GuestRequestController';
import { privacy } from '@/routes/website';
import type { Accommodation, Locale, RequestDetails } from '@/types/website';
const props = defineProps<{
    locale: Locale;
    copy: Record<string, string>;
    booking: boolean;
    accommodations: Accommodation[];
    token: string;
    selected: string | null;
    today: string;
    enabled: boolean;
    hasContacts: boolean;
    hasPrivacy: boolean;
    allowDirect?: boolean;
    context?: string;
    initial?: RequestDetails;
}>();
const emit = defineEmits<{ 'update:details': [details: RequestDetails] }>();
const formId = useId();
const fieldId = (name: string): string => `${formId}-${name}`;
const ar = computed(() => props.locale === 'ar');
const systemText = (en: string, arabic: string): string =>
    ar.value ? arabic : en;
const text = (key: string): string => props.copy[key] ?? '';
const summary = ref<HTMLElement | null>(null);
const connectionError = ref(false);
const form = useForm({
    name: '',
    email: '',
    phone: '',
    accommodation_id: props.initial?.accommodation_id ?? props.selected ?? '',
    arrival: props.initial?.arrival ?? '',
    departure: props.initial?.departure ?? '',
    guests: props.initial?.guests ?? 1,
    message: '',
    privacy: false,
    company: '',
    submission_token: props.token,
});
watch(
    () => props.initial,
    (initial, previous) => {
        if (!initial) return;
        if (initial.accommodation_id !== previous?.accommodation_id)
            form.accommodation_id = initial.accommodation_id;
        if (initial.arrival !== previous?.arrival)
            form.arrival = initial.arrival;
        if (initial.departure !== previous?.departure)
            form.departure = initial.departure;
        if (initial.guests !== previous?.guests) form.guests = initial.guests;
    },
    { deep: true },
);
watch(
    () => ({
        accommodation_id: form.accommodation_id,
        arrival: form.arrival,
        departure: form.departure,
        guests: form.guests,
    }),
    (details) => emit('update:details', details),
    { immediate: true },
);
const messages = computed(() => Object.values(form.errors));
async function focusErrors(): Promise<void> {
    await nextTick();
    summary.value?.focus();
}
function submit(): void {
    if (!props.enabled || form.processing) return;
    connectionError.value = false;
    form.transform((data) => ({
        ...data,
        message: [props.context, data.message].filter(Boolean).join('\n\n'),
    }));
    form.post((props.booking ? sendBooking : sendEnquiry)(props.locale).url, {
        preserveScroll: true,
        onError: () => {
            void focusErrors();
        },
        onNetworkError: () => {
            connectionError.value = true;
            void focusErrors();
            return false;
        },
    });
}
</script>
<template>
    <form class="resort-form" novalidate @submit.prevent="submit">
        <div v-if="!enabled" class="resort-notice" role="status">
            {{
                text(
                    hasContacts
                        ? 'form_contact_directly'
                        : 'form_requests_closed',
                )
            }}
        </div>
        <div
            v-if="messages.length || connectionError"
            ref="summary"
            tabindex="-1"
            role="alert"
            class="resort-errors"
        >
            <h2 class="font-semibold">
                {{
                    systemText(
                        'Please check your details',
                        'يرجى مراجعة البيانات',
                    )
                }}
            </h2>
            <ul>
                <li v-for="message in messages" :key="message">
                    {{ message }}
                </li>
            </ul>
            <p v-if="connectionError">
                {{
                    systemText(
                        'We could not connect. Your details are still here; please try again.',
                        'تعذر الاتصال. لا تزال بياناتك محفوظة هنا؛ يرجى المحاولة مجدداً.',
                    )
                }}
            </p>
        </div>
        <fieldset v-if="enabled || allowDirect" class="grid gap-6">
            <legend class="sr-only">
                {{
                    booking
                        ? text('form_booking_legend')
                        : text('form_enquiry_legend')
                }}
            </legend>
            <template v-if="booking">
                <div class="field">
                    <Label :for="fieldId('accommodation')">{{
                        text('form_accommodation')
                    }}</Label
                    ><select
                        :id="fieldId('accommodation')"
                        v-model="form.accommodation_id"
                        name="accommodation_id"
                        :aria-invalid="!!form.errors.accommodation_id"
                        :aria-describedby="fieldId('accommodation-error')"
                    >
                        <option value="">
                            {{ text('form_choose') }}
                        </option>
                        <option
                            v-for="stay in accommodations"
                            :key="stay.id"
                            :value="stay.id"
                        >
                            {{ stay.title }}
                        </option></select
                    ><InputError
                        :id="fieldId('accommodation-error')"
                        :message="form.errors.accommodation_id"
                    />
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="field">
                        <Label :for="fieldId('arrival')"
                            >{{ text('form_arrival') }} *</Label
                        ><Input
                            :id="fieldId('arrival')"
                            v-model="form.arrival"
                            name="arrival"
                            type="date"
                            :min="today"
                            required
                            :aria-invalid="!!form.errors.arrival"
                            :aria-describedby="fieldId('arrival-error')"
                        /><InputError
                            :id="fieldId('arrival-error')"
                            :message="form.errors.arrival"
                        />
                    </div>
                    <div class="field">
                        <Label :for="fieldId('departure')"
                            >{{ text('form_departure') }} *</Label
                        ><Input
                            :id="fieldId('departure')"
                            v-model="form.departure"
                            name="departure"
                            type="date"
                            :min="form.arrival || today"
                            required
                            :aria-invalid="!!form.errors.departure"
                            :aria-describedby="fieldId('departure-error')"
                        /><InputError
                            :id="fieldId('departure-error')"
                            :message="form.errors.departure"
                        />
                    </div>
                </div>
                <div class="field">
                    <Label :for="fieldId('guests')"
                        >{{ text('form_guests') }} *</Label
                    ><Input
                        :id="fieldId('guests')"
                        v-model="form.guests"
                        name="guests"
                        type="number"
                        min="1"
                        max="1000"
                        required
                        :aria-invalid="!!form.errors.guests"
                        :aria-describedby="fieldId('guests-error')"
                    /><InputError
                        :id="fieldId('guests-error')"
                        :message="form.errors.guests"
                    />
                </div>
            </template>
            <div class="field">
                <Label :for="fieldId('name')">{{ text('form_name') }} *</Label
                ><Input
                    :id="fieldId('name')"
                    v-model="form.name"
                    name="name"
                    autocomplete="name"
                    required
                    maxlength="180"
                    :aria-invalid="!!form.errors.name"
                    :aria-describedby="fieldId('name-error')"
                /><InputError
                    :id="fieldId('name-error')"
                    :message="form.errors.name"
                />
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div class="field">
                    <Label :for="fieldId('email')"
                        >{{ text('form_email') }} *</Label
                    ><Input
                        :id="fieldId('email')"
                        v-model="form.email"
                        name="email"
                        type="email"
                        dir="ltr"
                        autocomplete="email"
                        required
                        maxlength="254"
                        :aria-invalid="!!form.errors.email"
                        :aria-describedby="fieldId('email-error')"
                    /><InputError
                        :id="fieldId('email-error')"
                        :message="form.errors.email"
                    />
                </div>
                <div class="field">
                    <Label :for="fieldId('phone')"
                        >{{ text('form_phone') }} *</Label
                    ><Input
                        :id="fieldId('phone')"
                        v-model="form.phone"
                        name="phone"
                        type="tel"
                        dir="ltr"
                        autocomplete="tel"
                        required
                        maxlength="32"
                        :aria-invalid="!!form.errors.phone"
                        aria-describedby="phone-hint phone-error"
                    />
                    <p :id="fieldId('phone-hint')" class="resort-muted text-sm">
                        {{ text('form_phone_hint') }}
                    </p>
                    <InputError
                        :id="fieldId('phone-error')"
                        :message="form.errors.phone"
                    />
                </div>
            </div>
            <div class="field">
                <Label :for="fieldId('message')">{{
                    booking
                        ? text('form_booking_message')
                        : context
                          ? systemText(
                                'Details (optional)',
                                'التفاصيل (اختياري)',
                            )
                          : text('form_enquiry_message')
                }}</Label
                ><textarea
                    :id="fieldId('message')"
                    v-model="form.message"
                    name="message"
                    rows="4"
                    maxlength="5000"
                    :required="!booking && !context"
                    :aria-invalid="!!form.errors.message"
                    :aria-describedby="fieldId('message-error')"
                /><InputError
                    :id="fieldId('message-error')"
                    :message="form.errors.message"
                />
            </div>
            <div class="hidden" aria-hidden="true">
                <label :for="fieldId('company')">Company</label
                ><input
                    :id="fieldId('company')"
                    v-model="form.company"
                    name="company"
                    tabindex="-1"
                    autocomplete="off"
                />
            </div>
            <div>
                <label class="flex items-start gap-3 leading-relaxed"
                    ><input
                        v-model="form.privacy"
                        name="privacy"
                        type="checkbox"
                        required
                        class="mt-1 size-5 shrink-0 accent-[#276a70]"
                        :aria-invalid="!!form.errors.privacy"
                        :aria-describedby="fieldId('privacy-error')"
                    /><span
                        >{{ text('form_consent') }}
                        <Link
                            v-if="hasPrivacy"
                            :href="privacy(locale)"
                            target="_blank"
                            class="resort-link"
                            >{{ text('actions_privacy_notice') }}</Link
                        ></span
                    ></label
                ><InputError
                    :id="fieldId('privacy-error')"
                    :message="form.errors.privacy"
                />
            </div>
            <Button
                type="submit"
                class="resort-button w-full"
                :disabled="form.processing || !enabled"
                >{{
                    form.processing
                        ? systemText('Sending…', 'جارٍ الإرسال…')
                        : booking
                          ? text('form_send_booking')
                          : text('form_send_enquiry')
                }}</Button
            >
            <p class="resort-muted text-sm leading-relaxed">
                {{
                    systemText(
                        'Your request will be reviewed by our team. A reservation is confirmed only when staff sends you a confirmation.',
                        'سيراجع فريقنا طلبك. لا يُعد الحجز مؤكداً إلا بعد إرسال التأكيد من فريق المنتجع.',
                    )
                }}
            </p>
            <slot name="direct" :data="form" />
        </fieldset>
    </form>
</template>
