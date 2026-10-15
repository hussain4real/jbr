<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight } from '@lucide/vue';
import ResortPhoto from '@/components/website/ResortPhoto.vue';
import type { Accommodation } from '@/types/website';
import { useRedesign } from '@/composables/useRedesign';
defineProps<{ items: Accommodation[] }>();
const { stayLink, stayFacts, openBooking, label } = useRedesign();
</script>
<template>
    <div class="cards">
        <article v-for="stay in items" :key="stay.id" class="card">
            <Link :href="stayLink(stay)" class="im">
                <ResortPhoto
                    v-if="stay.images[0]"
                    :photo="stay.images[0]"
                    sizes="(max-width:620px) 92vw, (max-width:980px) 46vw, 30vw"
                    :fill="false"
                />
                <span v-if="stay.badge" class="badge">{{ stay.badge }}</span>
            </Link>
            <h3>
                <Link :href="stayLink(stay)">{{ stay.title }}</Link>
            </h3>
            <ul class="facts">
                <li
                    v-for="fact in stayFacts(stay).filter((fact) =>
                        ['Type', 'Ideal for', 'النوع', 'مثالية لـ'].includes(
                            fact.label,
                        ),
                    )"
                    :key="fact.label"
                >
                    {{ fact.value }}
                </li>
            </ul>
            <p>{{ stay.shortDescription || stay.description }}</p>
            <div class="acts">
                <Link class="link" :href="stayLink(stay)"
                    >{{ label('Discover', 'اكتشف') }} <ArrowUpRight /></Link
                ><button
                    type="button"
                    class="link"
                    @click="openBooking('stay', stay.slug)"
                >
                    {{ label('Check availability', 'استفسر عن التوفر') }}
                </button>
            </div>
        </article>
        <p v-if="!items.length" role="status">
            {{
                label(
                    'No stays in this category. Choose another filter.',
                    'لا توجد إقامات في هذه الفئة. اختر فئة أخرى.',
                )
            }}
        </p>
    </div>
</template>
