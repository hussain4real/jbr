<template>
    <Head title="Apartments" />
    <GuestLayout>
        <div class="container mx-auto py-4">
            <h1 class="text-4xl font-bold text-gray-800 mb-4 text-center">
                Apartments
            </h1>

            <ApartmentCards :apartments="apartments" />
            <!-- {{ apartments }} -->
        </div>
    </GuestLayout>
</template>
<script setup>
import { ref } from "vue";
import ApartmentCards from "@/Components/ApartmentCards.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import FilterComponent from "@/Components/Filter.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    apartments: {
        type: Object,
        required: true,
    },
});


// const { apartments } = toRefs(props);
</script>
