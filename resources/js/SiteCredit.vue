<script setup>
import { computed } from 'vue';

const props = defineProps({
    credit: { type: Object, required: true },
    theme: { type: String, default: null },
});

const resolvedTheme = computed(() => {
    const theme = props.theme ?? props.credit.theme;
    return ['inherit', 'light', 'dark'].includes(theme) ? theme : 'inherit';
});
const safeUrl = computed(() => {
    try {
        const url = new URL(props.credit.url);
        return ['https:', 'http:'].includes(url.protocol) ? props.credit.url : null;
    } catch {
        return null;
    }
});
</script>

<template>
    <p v-if="credit.enabled && safeUrl" class="bwd-site-credit" :data-theme="resolvedTheme">
        <span class="bwd-site-credit__label">{{ credit.label }}</span>
        {{ ' ' }}
        <a class="bwd-site-credit__link" :href="safeUrl" :title="credit.title">{{ credit.name }}</a>
    </p>
</template>
