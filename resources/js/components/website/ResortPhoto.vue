<script setup lang="ts">
import type { ResortImage } from '@/types/website';
withDefaults(
    defineProps<{
        photo: ResortImage;
        eager?: boolean;
        sizes?: string;
        fill?: boolean;
        fit?: 'cover' | 'contain';
    }>(),
    {
        eager: false,
        sizes: '(min-width: 1024px) 50vw, 100vw',
        fill: true,
        fit: 'cover',
    },
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
        :class="[
            fill && 'h-full w-full',
            fit === 'cover' ? 'object-cover' : 'object-contain',
        ]"
    />
</template>
