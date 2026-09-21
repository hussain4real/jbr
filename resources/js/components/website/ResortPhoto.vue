<script setup lang="ts">
import type { ResortImage } from '@/types/website';
withDefaults(
    defineProps<{ photo: ResortImage; eager?: boolean; sizes?: string }>(),
    { eager: false, sizes: '(min-width: 1024px) 50vw, 100vw' },
);
</script>
<template>
    <img
        :src="photo.src"
        :srcset="photo.srcset"
        :sizes="sizes"
        :width="photo.width"
        :height="photo.height"
        :alt="photo.alt"
        :style="{ objectPosition: photo.position }"
        :loading="eager ? 'eager' : 'lazy'"
        :fetchpriority="eager ? 'high' : 'auto'"
        decoding="async"
        class="h-full w-full object-cover"
    />
</template>
