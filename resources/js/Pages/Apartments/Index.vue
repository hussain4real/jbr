<template>
    <Head title="Apartments" />
    <GuestLayout>
        <div class="container mx-auto py-4">
            <h1 class="text-4xl font-bold text-gray-800 mb-4 text-center">
                Apartments
            </h1>

            <!-- Filter Component -->
            <div class="mb-8">
                <FilterComponent @updateFilters="applyFilters" />
            </div>
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

// Define state for filtered apartments
const filteredApartments = ref(props.apartments.data);

// Function to apply filters to the apartments
const applyFilters = (filters) => {
    filteredApartments.value = props.apartments.data.filter((apartment) => {
        // Filter by type
        if (filters.type && apartment.type !== filters.type) return false;

        // Filter by price range
        if (filters.minPrice !== null && apartment.price < filters.minPrice)
            return false;
        if (filters.maxPrice !== null && apartment.price > filters.maxPrice)
            return false;

        // Filter by bedrooms
        if (filters.bedrooms !== null && apartment.bedrooms < filters.bedrooms)
            return false;

        // Filter by bathrooms
        if (
            filters.bathrooms !== null &&
            apartment.bathrooms < filters.bathrooms
        )
            return false;

        // Filter by amenities (all selected amenities must be present)
        if (
            filters.amenities.length > 0 &&
            !filters.amenities.every((amenity) =>
                apartment.amenities.includes(amenity)
            )
        ) {
            return false;
        }

        return true;
    });
};

// const { apartments } = toRefs(props);
</script>
