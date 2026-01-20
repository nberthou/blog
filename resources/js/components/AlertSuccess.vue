<script setup lang="ts">
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { CheckCircle } from 'lucide-vue-next';

interface Props {
    message: string;
    title?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'Succès',
});
</script>

<template>
    <Alert variant="success">
        <CheckCircle class="size-4" />
        <AlertTitle>{{ title }}</AlertTitle>
        <AlertDescription>
            {{ message }}
        </AlertDescription>
    </Alert>
</template>
