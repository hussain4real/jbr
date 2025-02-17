<script setup>
import { computed, ref } from "vue";
import FilterComponent from "@/Components/Filter.vue";
const props = defineProps({
    apartments: Object,
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
const apartmentTypes = computed(() => {
    //return unique apartment types
    return [
        ...new Set(props.apartments.data.map((apartment) => apartment.type)),
    ];
});

const amenitiesOptions = computed(() => {
    //return unique amenities
    return [
        ...new Set(
            props.apartments.data.map((apartment) => apartment.amenities).flat()
        ),
    ];
});

const bedroomsNumber = computed(() => {
    //return unique bedrooms in ascending order
    return [
        ...new Set(props.apartments.data.map((apartment) => apartment.bedrooms)),
    ].sort((a, b) => a - b);
});
const bathroomsNumber = computed(() => {
    //return unique bathrooms in ascending order
    return [
        ...new Set(props.apartments.data.map((apartment) => apartment.bathrooms)),
    ].sort((a, b) => a - b);
});
</script>
<template>
    <!-- Filter Component -->
    <div class="mb-8">
        <FilterComponent
            @updateFilters="applyFilters"
            :apartmentTypes="apartmentTypes"
            :amenities-options="amenitiesOptions"
            :bedrooms-number="bedroomsNumber"
            :bathrooms-number="bathroomsNumber"
        />
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <div
            class="relative mx-auto w-full"
            v-for="apartment in filteredApartments"
        >
            <a
                href="https://www.google.com"
                class="relative inline-block w-full transform transition-transform duration-300 ease-in-out hover:-translate-y-2"
            >
                <div class="rounded-lg bg-white shadow max-h-full h-[28.5rem]">
                    <div
                        class="relative flex h-52 justify-center overflow-hidden rounded-lg"
                    >
                        <div
                            class="w-full transform transition-transform duration-500 ease-in-out hover:scale-110"
                        >
                            <div
                                class="absolute inset-0 bg-black bg-opacity-80"
                            >
                                <img
                                    src="https://assets.entrepreneur.com/content/3x2/2000/20150622231001-for-sale-real-estate-home-house.jpeg?crop=16:9"
                                    alt=""
                                />
                            </div>
                        </div>

                        <div class="absolute bottom-0 left-5 mb-3 flex">
                            <p
                                class="flex items-center font-medium text-white shadow-sm"
                            >
                                <!-- <i
                                    class="fa fa-camera mr-2 text-xl text-white"
                                ></i>
                                10 -->
                                <font-awesome-layers full-width>
                                    <font-awesome-icon
                                        :icon="['fas', 'camera']"
                                        class="text-white"
                                    />
                                    <font-awesome-layers-text
                                        value="10"
                                        position="top-right"
                                        transform="down-1 shrink-3"
                                        class="text-rose-400 font-bold"
                                    />
                                </font-awesome-layers>
                            </p>
                        </div>
                        <div class="absolute bottom-0 right-5 mb-3 flex">
                            <p
                                class="flex items-center font-medium text-gray-800"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="white"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="white"
                                    class="size-6"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
                                    />
                                </svg>
                            </p>
                        </div>

                        <span
                            class="absolute top-0 right-2 z-10 mt-3 ml-3 inline-flex select-none rounded-sm bg-[#1f93ff] px-2 py-1 text-xs font-semibold text-white"
                        >
                            {{ apartment.type }}
                        </span>
                        <span
                            class="absolute top-0 left-0 z-10 mt-3 ml-3 inline-flex select-none rounded-lg bg-transparent px-3 py-2 text-lg font-medium text-white"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="size-6"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"
                                />
                            </svg>
                        </span>
                    </div>

                    <div class="px-2 py-1">
                        <div class="mt-2">
                            <h2
                                class="line-clamp-1 text-2xl font-medium text-gray-800 md:text-lg"
                                title="New York"
                            >
                                {{ apartment.name }}
                            </h2>

                            <p
                                class="text-primary mt-2 inline-block whitespace-nowrap rounded-xl font-semibold leading-tight"
                            >
                                <span class="text-sm uppercase"> OMR </span>
                                <span class="text-2xl">{{
                                    apartment.price
                                }}</span
                                >/month
                            </p>
                        </div>
                        <div class="mt-4">
                            <p class="line-clamp-1 mt-2 text-lg text-gray-800">
                                {{ apartment.description }}
                            </p>
                        </div>
                        <div class="justify-center">
                            <div
                                class="mt-4 flex space-x-6 overflow-hidden rounded-lg px-1 pt-4"
                            >
                                <p
                                    class="flex items-center font-medium text-gray-800"
                                >
                                    <font-awesome-layers
                                        full-width
                                        class="ml-2"
                                    >
                                        <font-awesome-icon
                                            :icon="['fas', 'bed']"
                                            class="text-teal-600"
                                        />
                                        <font-awesome-layers-text
                                            value="5"
                                            position="top-right"
                                            transform="down-0 up-2 shrink-3"
                                            class="text-orange-400 font-bold"
                                        />
                                    </font-awesome-layers>
                                </p>

                                <p
                                    class="flex items-center font-medium text-gray-800"
                                >
                                    <font-awesome-layers full-width>
                                        <font-awesome-icon
                                            :icon="['fas', 'bath']"
                                            class="text-teal-600"
                                        />
                                        <font-awesome-layers-text
                                            :value="apartment.bathrooms"
                                            position="top-right"
                                            transform="down-0 up-2 shrink-3"
                                            class="text-orange-400 font-bold"
                                        />
                                    </font-awesome-layers>
                                </p>
                                <p
                                    class="flex items-center font-medium text-gray-800"
                                >
                                    <font-awesome-layers full-width>
                                        <font-awesome-icon
                                            :icon="['fas', 'house']"
                                            class="text-teal-600"
                                        />
                                        <font-awesome-layers-text
                                            :value="`${apartment.area}sqm`"
                                            position="top-right"
                                            transform="down-0 up-2 shrink-3"
                                            class="text-orange-400 font-bold"
                                        />
                                    </font-awesome-layers>
                                    <!-- 2000 Yd<sup>2</sup> -->
                                </p>
                            </div>
                        </div>
                        <div class="mt-6 grid grid-cols-3 content-end">
                            <div
                                class="flex items-baseline justify-start flex-wrap gap-2 col-span-2"
                            >
                                <ul
                                    class="flex overflow-ellipsis"
                                    v-for="amenities in apartment.amenities"
                                >
                                    <li
                                        class="mx-1 px-1 text-sm text-gray-800 bg-slate-300/40 rounded shadow capitalize hover:bg-teal-500 hover:text-white text-wrap"
                                    >
                                        {{ amenities }}
                                    </li>
                                </ul>
                                <!-- <div class="relative">
                                <div
                                    class="h-6 w-6 rounded-full bg-gray-200 md:h-8 md:w-8"
                                ></div>
                                <span
                                    class="bg-primary-red absolute top-0 right-0 inline-block h-3 w-3 rounded-full"
                                ></span>
                            </div>

                            <p class="line-clamp-1 ml-2 text-gray-800">
                                Salman Ghouri Dev
                            </p> -->
                            </div>

                            <div class="flex justify-end items-baseline">
                                <!-- <button
                                class="flex items-center px-4 py-2 text-sm font-medium text-white bg-red-400 rounded-lg"
                            >
                                View Details
                            </button> -->
                                <button
                                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-400 rounded-lg hover:bg-teal-500"
                                >
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>
