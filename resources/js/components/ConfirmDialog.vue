<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Props {
    open: boolean;
    title?: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    confirmVariant?:
        | 'default'
        | 'destructive'
        | 'outline'
        | 'secondary'
        | 'ghost';
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Confirmer',
    description: 'Êtes-vous sûr de vouloir continuer ?',
    confirmLabel: 'Confirmer',
    cancelLabel: 'Annuler',
    confirmVariant: 'destructive',
    loading: false,
});

const emit = defineEmits<{
    'update:open': [value: boolean];
    confirm: [];
    cancel: [];
}>();

const handleConfirm = () => {
    emit('confirm');
};

const handleCancel = () => {
    emit('cancel');
    emit('update:open', false);
};
</script>

<template>
    <Dialog
        :open="props.open"
        @update:open="(value) => emit('update:open', value)"
    >
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ props.title }}</DialogTitle>
                <DialogDescription>
                    <slot name="description">
                        {{ props.description }}
                    </slot>
                </DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <DialogClose as-child>
                    <Button variant="secondary" @click="handleCancel">
                        {{ props.cancelLabel }}
                    </Button>
                </DialogClose>
                <Button
                    :variant="props.confirmVariant"
                    :disabled="props.loading"
                    @click="handleConfirm"
                >
                    {{ props.loading ? 'Chargement...' : props.confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
