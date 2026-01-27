<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { useForm } from '@inertiajs/vue3';
import { Send } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    postSlug: string;
    parentId?: number | null;
    placeholder?: string;
    buttonLabel?: string;
    autofocus?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    parentId: null,
    placeholder: 'Votre commentaire...',
    buttonLabel: 'Commenter',
    autofocus: false,
});

const emit = defineEmits<{
    submitted: [];
}>();

const form = useForm({
    content: '',
    parent_id: props.parentId,
});

const storeUrl = computed(() => `/posts/${props.postSlug}/comments`);

const submit = () => {
    form.post(storeUrl.value, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('content');
            emit('submitted');
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-3">
        <Textarea
            v-model="form.content"
            :placeholder="placeholder"
            :autofocus="autofocus"
            rows="3"
            class="resize-none"
            :class="{ 'border-destructive': form.errors.content }"
        />
        <p v-if="form.errors.content" class="text-sm text-destructive">
            {{ form.errors.content }}
        </p>
        <div class="flex justify-end">
            <Button
                type="submit"
                size="sm"
                :disabled="form.processing || !form.content.trim()"
            >
                <Send class="mr-2 h-4 w-4" />
                {{ buttonLabel }}
            </Button>
        </div>
    </form>
</template>
