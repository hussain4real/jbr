<script setup>
import { computed, toRefs } from "vue";

const props = defineProps({
    apartments: Array,
});

const { apartments } = toRefs(props);
</script>
<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
            v-for="apartment in apartments"
            :key="apartment.id"
            class="bg-white rounded-lg shadow-lg"
        >
            <img
                src="https://via.placeholder.com/150"
                :alt="apartment.name"
                class="w-full h-48 object-cover rounded-t-lg"
            />
            <div class="p-4">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ apartment.name }}
                </h2>
                <p class="text-gray-600 mt-2">
                    {{ apartment.description }}
                </p>
                
                <p class="text-orange-500 font-bold mt-4">
                    ${{ apartment.price }}/month
                </p>
            </div>
        </div>
    </div>
</template>
