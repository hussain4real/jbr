<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes/website';
</script>
<template>
    <div><Link :href="home('en')">Jawharat Bidiyah Resort</Link></div>
</template>
