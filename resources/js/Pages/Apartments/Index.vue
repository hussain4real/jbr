<template>
    <Head title="Apartments" />
    <GuestLayout>
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Apartments</h1>
            <ApartmentCards :apartments="apartments" />
        </div>
    </GuestLayout>
</template>
<script setup>
import { ref, toRefs } from "vue";
import ApartmentCards from "@/Components/ApartmentCards.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head } from "@inertiajs/vue3";

const props = defineProps({
    apartments: {
        type: Array,
        required: true,
    },
});

// const { apartments } = toRefs(props);
</script>
