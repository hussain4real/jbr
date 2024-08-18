<script setup>
import { onMounted } from 'vue';
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    encRequest: String,
    accessCode: String,
    actionUrl: String,
});

onMounted(() => {
    document.getElementById('paymentForm').submit();
});
</script>

<template>
    <GuestLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Payment initiation process
            </h2>
        </template>
        <div>
            <form id="paymentForm" method="POST" :action="props.actionUrl">
                <input type="hidden" name="encRequest" :value="props.encRequest" />
                <input type="hidden" name="access_code" :value="props.accessCode" />
            </form>
            <p>Redirecting to payment gateway...</p>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Add any necessary styles here */
</style>
