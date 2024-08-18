<script setup>
import { computed } from 'vue';
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    response: Object,  // Changed from String to Object to handle the parsed response directly
    status: String,    // Add status to display success or failure
    message: String,   // Add message to display the result message
});

const parsedResponse = computed(() => {
    return props.response;
});
</script>

<template>
    <GuestLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Payment Response
            </h2>
        </template>
        <div class="max-w-2xl mx-auto mt-6">
            <div v-if="status === 'success'" class="bg-green-100 text-green-800 p-4 rounded">
                <h3 class="font-semibold text-lg">Payment Successful</h3>
                <p>{{ message }}</p>
            </div>
            <div v-if="status === 'failure'" class="bg-red-100 text-red-800 p-4 rounded">
                <h3 class="font-semibold text-lg">Payment Failed</h3>
                <p>{{ message }}</p>
            </div>

            <div v-if="parsedResponse" class="mt-4">
                <h4 class="font-semibold text-lg mb-2">Transaction Details:</h4>
                <ul class="list-disc list-inside space-y-2">
                    <li v-for="(value, key) in parsedResponse" :key="key">
                        <strong>{{ key }}:</strong> {{ value }}
                    </li>
                </ul>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Add any necessary styles here */
</style>
