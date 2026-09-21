<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { login } from '@/routes';
</script>
<template>
    <div>
        <Head title="Staff access" />
        <p>Staff accounts are created by the resort administrator.</p>
        <Link :href="login()">Log in</Link>
    </div>
</template>
