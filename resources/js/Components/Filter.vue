<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    apartmentTypes: {
        type: Array,
        required: true,
    },
    amenitiesOptions: {
        type: Array,
        required: true,
    },
    bedroomsNumber: {
        type: Array,
        required: true,
    },
    bathroomsNumber: {
        type: Array,
        required: true,
    },
});
// Define the available filter options (you can update this based on your data)
//get apartment types as props
const apartmentTypes = ref(props.apartmentTypes);
const amenitiesOptions = ref(props.amenitiesOptions);
const priceRange = ref([0, 10000]);
const bedroomsNumber = ref(props.bedroomsNumber);
const bathroomsNumber = ref(props.bathroomsNumber);

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
        console.log("filters", newFilters);
    },
    { deep: true }
);
</script>

<template>
    <div class="p-4 bg-white rounded-lg shadow">
        <h3 class="text-xl font-bold mb-4">Filter Apartments</h3>

        <div class="flex w-full space-x-4">
            <!-- Filter by Type -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Type</label
                >
                <select
                    v-model="filters.type"
                    class="form-select w-full border-gray-300 rounded-lg"
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
                        class="form-input w-full border-gray-300 rounded-lg"
                    />
                    <input
                        v-model="filters.maxPrice"
                        type="number"
                        placeholder="Max Price"
                        class="form-input w-full border-gray-300 rounded-lg"
                    />
                </div>
            </div>

            <!-- Filter by Bedrooms -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Bedrooms</label
                >
                <select
                    v-model="filters.bedrooms"
                    class="form-select w-full border-gray-300 rounded-lg"
                >
                    <option value="">Any</option>
                    <option
                        v-for="bedroom in bedroomsNumber"
                        :key="bedroom"
                        :value="bedroom"
                    >
                        {{ bedroom }}
                    </option>
                </select>
            </div>

            <!-- Filter by Bathrooms -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Bathrooms</label
                >
                <select
                    v-model="filters.bathrooms"
                    class="form-select w-full border-gray-300 rounded-lg"
                >
                    <option value="">Any</option>
                    <option
                        v-for="bathroom in bathroomsNumber"
                        :key="bathroom"
                        :value="bathroom"
                    >
                        {{ bathroom }}
                    </option>
                </select>
            </div>
            <!-- Filter by Amenities -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1"
                    >Amenities</label
                >
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                    <label
                        v-for="amenity in amenitiesOptions"
                        :key="amenity"
                        class="inline-flex items-center"
                    >
                        <input
                            type="checkbox"
                            v-model="filters.amenities"
                            :value="amenity"
                            class="form-checkbox rounded text-purple-500 h-5 w-5"
                        />
                        <span class="ml-2">{{ amenity }}</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>
