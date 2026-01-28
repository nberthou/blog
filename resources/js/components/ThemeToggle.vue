<script setup lang="ts">
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuPortal,
    DropdownMenuSeparator,
    DropdownMenuSub,
    DropdownMenuSubContent,
    DropdownMenuSubTrigger,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';
import { Check, Monitor, Moon, Sun } from 'lucide-vue-next';

const { appearance, updateAppearance } = useAppearance();

const themes = [
    { value: 'light', label: 'Clair', icon: Sun },
    { value: 'dark', label: 'Sombre', icon: Moon },
    { value: 'system', label: 'Systeme', icon: Monitor },
] as const;
</script>

<template>
    <DropdownMenuGroup>
        <DropdownMenuSub>
            <DropdownMenuSubTrigger>
                <Sun class="mr-2 h-4 w-4 dark:hidden" />
                <Moon class="mr-2 hidden h-4 w-4 dark:block" />
                Theme
            </DropdownMenuSubTrigger>
            <DropdownMenuPortal>
                <DropdownMenuSubContent>
                    <DropdownMenuLabel class="text-xs text-muted-foreground">
                        Apparence
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        v-for="theme in themes"
                        :key="theme.value"
                        @click="updateAppearance(theme.value)"
                    >
                        <component :is="theme.icon" class="mr-2 h-4 w-4" />
                        {{ theme.label }}
                        <Check
                            v-if="appearance === theme.value"
                            class="ml-auto h-4 w-4"
                        />
                    </DropdownMenuItem>
                </DropdownMenuSubContent>
            </DropdownMenuPortal>
        </DropdownMenuSub>
    </DropdownMenuGroup>
</template>
