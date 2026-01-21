<script setup lang="ts">

import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import AuthDialog from "@/components/AuthDialog.vue";
import { Link } from '@inertiajs/vue3';
import { login, register} from "@/routes";

</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-cebter gap-2 px-1 py-1.5 text-left text-sm">
            Vous n'êtes pas connecté(e).
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <AuthDialog type="LOGIN" />
        <AuthDialog type="REGISTER" />
    </DropdownMenuGroup>
</template>

<style scoped>

</style>
