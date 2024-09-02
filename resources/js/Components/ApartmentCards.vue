<script setup>
import { computed, toRefs } from "vue";

const props = defineProps({
    apartments: Array,
});

const { apartments } = toRefs(props);

// const featuredApartments = computed(() =>
//   apartments.value.filter(apartment => apartment.featured)
// );
</script>
<template>
    <div class="relative p-4">
      <!-- Scrollable container -->
      <div class="flex space-x-4 overflow-x-auto snap-x snap-mandatory hide-scroll-bar">
        <div
          v-for="apartment in apartments"
          :key="apartment.id"
          class="flex-shrink-0 w-64 bg-white rounded-lg shadow-lg snap-always snap-end"
        >
          <img
            :src="apartment.image"
            alt="Apartment Image"
            class="w-full h-40 object-cover rounded-t-lg"
          />
          <div class="p-4">
            <h2 class="text-xl font-semibold text-gray-800">
              {{ apartment.name }}
            </h2>
            <p class="text-gray-600 mt-2">{{ apartment.description }}</p>
            <p class="text-orange-500 font-bold mt-4">
              ${{ apartment.price }}/month
            </p>
          </div>
        </div>
      </div>
  
      <!-- Gradient Overlay for Scroll Indicator -->
      <div class="absolute top-0 left-0 w-4 h-full bg-gradient-to-r from-white pointer-events-none"></div>
      <div class="absolute top-0 right-0 w-4 h-full bg-gradient-to-l from-white pointer-events-none"></div>
    </div>
  </template>
  
  <style scoped>
  .hide-scroll-bar {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  
  .hide-scroll-bar::-webkit-scrollbar {
    display: none;
  }
  </style>
  
