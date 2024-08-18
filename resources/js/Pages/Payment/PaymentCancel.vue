<script setup>

import GuestLayout from "@/Layouts/GuestLayout.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    response: Object,
    status: String,
    message: String,
    merchantId: String,
    accessCode: String,
});

const form = useForm({
    merchant_id: props.merchantId,
    access_code: props.accessCode,
   //generate a new order id
    order_id: Math.floor(Math.random() * 1000000),
    tid: props.response.tracking_id,
    amount: props.response.amount,
    currency: props.response.currency,
    redirect_url: route('payment-response'),
    cancel_url: route('payment.cancel'),
})

const submitForm = () => {
    form.post(route('initiate-transaction'));
}
</script>

<template>
    <GuestLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Payment Cancelled
            </h2>
        </template>
        <div class="max-w-2xl mx-auto mt-6">
            <div class="bg-red-100 text-red-800 p-4 rounded">
                <h3 class="font-semibold text-lg">{{message}}</h3>
                <p>Payment process was cancelled by the user.</p>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="merchant_id" class="block text-sm font-medium text-gray-700">Merchant ID:</label>
                            <input v-model="form.merchant_id" type="text" id="merchant_id" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="access_code" class="block text-sm font-medium text-gray-700">Access Code:</label>
                            <input v-model="form.access_code" type="text" id="access_code" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="order_id" class="block text-sm font-medium text-gray-700">Order ID:</label>
                            <input v-model="form.order_id" type="text" id="order_id" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="tid" class="block text-sm font-medium text-gray-700">Transaction Unique ID:</label>
                            <input v-model="form.tid" type="text" id="tid" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700">Amount:</label>
                            <input v-model="form.amount" type="text" id="amount" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="currency" class="block text-sm font-medium text-gray-700">Currency:</label>
                            <input v-model="form.currency" type="text" id="currency" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="redirect_url" class="block text-sm font-medium text-gray-700">Redirect URL:</label>
                            <input v-model="form.redirect_url" type="text" id="redirect_url" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                        <div>
                            <label for="cancel_url" class="block text-sm font-medium text-gray-700">Cancel URL:</label>
                            <input v-model="form.cancel_url" type="text" id="cancel_url" required
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                        </div>
                    </div>
                    <div class="flex justify-center mt-6">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            Try Again
                        </button>
                        <button @click.prevent="$inertia.visit(route('welcome'))" type="button" class="ml-4 px-4 py-2 bg-gray-500 text-white font-semibold rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring focus:ring-gray-500 focus:ring-opacity-50">
                            Go Home
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>

</style>
