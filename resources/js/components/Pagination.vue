<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { PaginationLink } from '@/types/post';
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

interface Props {
    links: PaginationLink[];
    prevPageUrl: string | null;
    nextPageUrl: string | null;
    from: number | null;
    to: number | null;
    total: number;
}

defineProps<Props>();
</script>

<template>
    <div
        class="flex flex-col items-center justify-between gap-4 sm:flex-row"
        v-if="total > 0"
    >
        <p class="text-sm text-muted-foreground">
            Affichage de
            <span class="font-medium">{{ from }}</span>
            à
            <span class="font-medium">{{ to }}</span>
            sur
            <span class="font-medium">{{ total }}</span>
            résultats
        </p>

        <div class="flex items-center gap-1">
            <Button
                v-if="prevPageUrl"
                variant="outline"
                size="icon-sm"
                as-child
            >
                <Link :href="prevPageUrl" preserve-scroll>
                    <ChevronLeft class="h-4 w-4" />
                </Link>
            </Button>
            <Button v-else variant="outline" size="icon-sm" disabled>
                <ChevronLeft class="h-4 w-4" />
            </Button>

            <template v-for="(link, index) in links" :key="index">
                <Button
                    v-if="
                        link.url &&
                        !link.label.includes('Previous') &&
                        !link.label.includes('Next')
                    "
                    :variant="link.active ? 'default' : 'outline'"
                    size="icon-sm"
                    as-child
                >
                    <Link :href="link.url" preserve-scroll>
                        {{ link.label }}
                    </Link>
                </Button>
                <span
                    v-else-if="
                        !link.label.includes('Previous') &&
                        !link.label.includes('Next')
                    "
                    class="px-2 text-sm text-muted-foreground"
                >
                    {{ link.label }}
                </span>
            </template>

            <Button
                v-if="nextPageUrl"
                variant="outline"
                size="icon-sm"
                as-child
            >
                <Link :href="nextPageUrl" preserve-scroll>
                    <ChevronRight class="h-4 w-4" />
                </Link>
            </Button>
            <Button v-else variant="outline" size="icon-sm" disabled>
                <ChevronRight class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
