<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    accessCode: String,
    merchantId: String,
    redirectUrl: String,
    cancelUrl: String,
    orderId: String,
});

const form = useForm({
    merchant_id: props.merchantId,
    access_code: props.accessCode,
    order_id: props.orderId,
    tid: "",
    instant_gratification: "",
    saveCard: "",
    customer_identifier: "",
    customer_card_id: "",
    amount: "150",
    currency: "OMR",
    redirect_url: props.redirectUrl,
    cancel_url: props.cancelUrl,
    merchant_param1: "Mobile 55481589",
    merchant_param2: "Accommodation for 2",
    merchant_param3: "Bidiyah Resort",
    merchant_param4: "",
    merchant_param5: "",
    card_number: "",
    expiry_month: "",
    expiry_year: "",
    cvv_number: "",
    si_type: "",
    si_mer_ref_no: "",
    si_amount: "",
    integration_type: "",
    billing_name: "Aminu Hussain",
    billing_address: "Room no 1101, Bin Mahmoud",
    billing_city: "Doha",
    billing_state: "Ad Dawhah",
    billing_zip: "00000",
    billing_country: "Qatar",
    billing_tel: "55481589",
    billing_email: "aminu.hussain@tamkeen-edu.com",
});

const submitForm = () => {
    form.post(route("initiate-transaction"));
};

onMounted(() => {
    form.tid = new Date().getTime().toString();
});
</script>

<template>
    <GuestLayout>
        <template #header>
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
                Payment Initiation Process
            </h2>
        </template>
        <div class="max-w-7xl mx-auto p-6">
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
                        <label for="instant_gratification" class="block text-sm font-medium text-gray-700">Instant Gratification:</label>
                        <input v-model="form.instant_gratification" type="text" id="instant_gratification"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="saveCard" class="block text-sm font-medium text-gray-700">Save Card:</label>
                        <select v-model="form.saveCard" id="saveCard"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Select Type</option>
                            <option value="Y">Yes</option>
                            <option value="N">No</option>
                        </select>
                    </div>
                    <div>
                        <label for="customer_identifier" class="block text-sm font-medium text-gray-700">Customer ID:</label>
                        <input v-model="form.customer_identifier" type="text" id="customer_identifier"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="customer_card_id" class="block text-sm font-medium text-gray-700">Customer Card ID:</label>
                        <input v-model="form.customer_card_id" type="text" id="customer_card_id"
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
                    <div>
                        <label for="merchant_param1" class="block text-sm font-medium text-gray-700">Merchant Parameter1:</label>
                        <input v-model="form.merchant_param1" type="text" id="merchant_param1"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="merchant_param2" class="block text-sm font-medium text-gray-700">Merchant Parameter2:</label>
                        <input v-model="form.merchant_param2" type="text" id="merchant_param2"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="merchant_param3" class="block text-sm font-medium text-gray-700">Merchant Parameter3:</label>
                        <input v-model="form.merchant_param3" type="text" id="merchant_param3"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="merchant_param4" class="block text-sm font-medium text-gray-700">Merchant Parameter4:</label>
                        <input v-model="form.merchant_param4" type="text" id="merchant_param4"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="merchant_param5" class="block text-sm font-medium text-gray-700">Merchant Parameter5:</label>
                        <input v-model="form.merchant_param5" type="text" id="merchant_param5"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="card_number" class="block text-sm font-medium text-gray-700">Card Number:</label>
                        <input v-model="form.card_number" type="text" id="card_number"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="expiry_month" class="block text-sm font-medium text-gray-700">Expiry Month:</label>
                        <input v-model="form.expiry_month" type="text" id="expiry_month"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="expiry_year" class="block text-sm font-medium text-gray-700">Expiry Year:</label>
                        <input v-model="form.expiry_year" type="text" id="expiry_year"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="cvv_number" class="block text-sm font-medium text-gray-700">CVV Number:</label>
                        <input v-model="form.cvv_number" type="text" id="cvv_number"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="si_type" class="block text-sm font-medium text-gray-700">SI Type:</label>
                        <select v-model="form.si_type" id="si_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Select SI Type</option>
                            <option value="ONDEMAND">On Demand</option>
                        </select>
                    </div>
                    <div>
                        <label for="si_mer_ref_no" class="block text-sm font-medium text-gray-700">SI Merchant Ref No:</label>
                        <input v-model="form.si_mer_ref_no" type="text" id="si_mer_ref_no"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="si_amount" class="block text-sm font-medium text-gray-700">SI Amount:</label>
                        <input v-model="form.si_amount" type="text" id="si_amount"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="integration_type" class="block text-sm font-medium text-gray-700">Integration Type:</label>
                        <input v-model="form.integration_type" type="text" id="integration_type"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_name" class="block text-sm font-medium text-gray-700">Billing Name:</label>
                        <input v-model="form.billing_name" type="text" id="billing_name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_address" class="block text-sm font-medium text-gray-700">Billing Address:</label>
                        <input v-model="form.billing_address" type="text" id="billing_address"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_city" class="block text-sm font-medium text-gray-700">Billing City:</label>
                        <input v-model="form.billing_city" type="text" id="billing_city"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_state" class="block text-sm font-medium text-gray-700">Billing State:</label>
                        <input v-model="form.billing_state" type="text" id="billing_state"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_zip" class="block text-sm font-medium text-gray-700">Billing Zip:</label>
                        <input v-model="form.billing_zip" type="text" id="billing_zip"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_country" class="block text-sm font-medium text-gray-700">Billing Country:</label>
                        <input v-model="form.billing_country" type="text" id="billing_country"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_tel" class="block text-sm font-medium text-gray-700">Billing Tel:</label>
                        <input v-model="form.billing_tel" type="text" id="billing_tel"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                    <div>
                        <label for="billing_email" class="block text-sm font-medium text-gray-700">Billing Email:</label>
                        <input v-model="form.billing_email" type="text" id="billing_email"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"/>
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>

<style scoped>
/* Add any additional scoped styles here */
</style>
