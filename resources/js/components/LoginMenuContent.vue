<script setup lang="ts">
import AuthDialog from '@/components/AuthDialog.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import {
    DropdownMenuGroup,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';

const emit = defineEmits<{
    close: [];
}>();
</script>

<template>
    <DropdownMenuLabel
        class="relative flex w-full items-center rounded-sm px-2 py-1.5 text-sm outline-none select-none"
    >
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            Vous n'êtes pas connecté(e).
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <AuthDialog type="LOGIN" @success="emit('close')" />
        <DropdownMenuSeparator />
        <AuthDialog type="REGISTER" @success="emit('close')" />
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <ThemeToggle />
</template>
