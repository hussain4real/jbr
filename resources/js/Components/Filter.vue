<script setup>
import { ref, watch } from "vue";

// Define the available filter options (you can update this based on your data)
const apartmentTypes = ["Apartment", "Villa", "House"];
const amenitiesOptions = ["Wifi", "Kitchen", "TV", "Gym", "Pool", "Parking"];
const priceRange = ref([0, 1000]);

const filters = ref({
    type: "",
    minPrice: null,
    maxPrice: null,
    bedrooms: null,
    bathrooms: null,
    amenities: [],
});

// Emit the filters whenever they are updated
const emit = defineEmits(["updateFilters"]);

watch(
    filters,
    (newFilters) => {
        emit("updateFilters", newFilters);
    },
    { deep: true }
);
</script>

<template>
    <div class="p-4 bg-white rounded-lg shadow">
        <h3 class="text-xl font-bold mb-4">Filter Apartments</h3>

        <!-- Filter by Type -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1"
                >Type</label
            >
            <select
                v-model="filters.type"
                class="w-full border-gray-300 rounded-lg"
            >
                <option value="">All Types</option>
                <option
                    v-for="type in apartmentTypes"
                    :key="type"
                    :value="type"
                >
                    {{ type }}
                </option>
            </select>
        </div>

        <!-- Filter by Price Range -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1"
                >Price Range (OMR)</label
            >
            <div class="flex space-x-2">
                <input
                    v-model="filters.minPrice"
                    type="number"
                    placeholder="Min Price"
                    class="w-full border-gray-300 rounded-lg"
                />
                <input
                    v-model="filters.maxPrice"
                    type="number"
                    placeholder="Max Price"
                    class="w-full border-gray-300 rounded-lg"
                />
            </div>
        </div>

        <!-- Filter by Bedrooms -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1"
                >Bedrooms</label
            >
            <input
                v-model="filters.bedrooms"
                type="number"
                placeholder="Any"
                class="w-full border-gray-300 rounded-lg"
            />
        </div>

        <!-- Filter by Bathrooms -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1"
                >Bathrooms</label
            >
            <input
                v-model="filters.bathrooms"
                type="number"
                placeholder="Any"
                class="w-full border-gray-300 rounded-lg"
            />
        </div>

        <!-- Filter by Amenities -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1"
                >Amenities</label
            >
            <div class="grid grid-cols-2 gap-2">
                <label
                    v-for="amenity in amenitiesOptions"
                    :key="amenity"
                    class="inline-flex items-center"
                >
                    <input
                        type="checkbox"
                        v-model="filters.amenities"
                        :value="amenity"
                        class="form-checkbox h-5 w-5"
                    />
                    <span class="ml-2">{{ amenity }}</span>
                </label>
            </div>
        </div>
    </div>
</template>
