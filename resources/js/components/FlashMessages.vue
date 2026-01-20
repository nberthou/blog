<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import AlertSuccess from '@/components/AlertSuccess.vue';
import { computed, ref, watch } from 'vue';

const page = usePage();

const flash = computed(() => page.props.flash as { success?: string; error?: string } | undefined);

const showSuccess = ref(false);
const successMessage = ref('');

watch(() => flash.value?.success, (message) => {
    if (message) {
        successMessage.value = message;
        showSuccess.value = true;
        setTimeout(() => {
            showSuccess.value = false;
        }, 5000);
    }
}, { immediate: true });
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-[-10px]"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-[-10px]"
    >
        <div v-if="showSuccess" class="fixed top-4 right-4 z-50 max-w-sm">
            <AlertSuccess :message="successMessage" />
        </div>
    </Transition>
</template>
