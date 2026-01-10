<script setup lang="ts">

import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Link} from '@inertiajs/vue3';
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
        <DropdownMenuItem :as-child>
            <Link :href="login()">Se connecter</Link>
        </DropdownMenuItem>
        <DropdownMenuItem :as-child>
            <Link :href="register()">S'inscrire</Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>
</template>

<style scoped>

</style>
