<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { home } from '@/routes';
import type { Category } from '@/types/post';
import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { Search, X } from 'lucide-vue-next';
import { ref, watch } from 'vue';

interface Props {
    categories: Category[];
    filters: {
        category: string | null;
        search: string | null;
    };
}

const props = defineProps<Props>();

const searchInput = ref(props.filters.search ?? '');

const navigateWithFilters = (params: {
    category?: string | null;
    search?: string | null;
}) => {
    const query: Record<string, string | undefined> = {};

    if (params.category !== undefined) {
        query.category = params.category || undefined;
    } else if (props.filters.category) {
        query.category = props.filters.category;
    }

    if (params.search !== undefined) {
        query.search = params.search || undefined;
    } else if (props.filters.search) {
        query.search = props.filters.search;
    }

    router.get(home(), query, {
        preserveState: true,
        preserveScroll: true,
    });
};

const selectCategory = (slug: string | null) => {
    navigateWithFilters({ category: slug });
};

const debouncedSearch = useDebounceFn((value: string) => {
    navigateWithFilters({ search: value || null });
}, 300);

watch(searchInput, (value) => {
    debouncedSearch(value);
});

const clearSearch = () => {
    searchInput.value = '';
    navigateWithFilters({ search: null });
};
</script>

<template>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-6">
        <!-- Categories with horizontal scroll -->
        <div class="min-w-0 flex-1">
            <div
                class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-2 sm:-mx-6 sm:px-6 lg:mx-0 lg:px-0 [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-border [&::-webkit-scrollbar-track]:bg-transparent"
            >
                <Button
                    :variant="!filters.category ? 'default' : 'outline'"
                    size="sm"
                    class="shrink-0"
                    @click="selectCategory(null)"
                >
                    Tous
                </Button>
                <Button
                    v-for="category in categories"
                    :key="category.id"
                    :variant="
                        filters.category === category.slug
                            ? 'default'
                            : 'outline'
                    "
                    size="sm"
                    class="shrink-0 whitespace-nowrap"
                    @click="selectCategory(category.slug)"
                >
                    {{ category.name }}
                    <span class="ml-1.5 text-xs opacity-70">
                        ({{ category.posts_count }})
                    </span>
                </Button>
            </div>
        </div>

        <!-- Search Input -->
        <div class="relative w-full shrink-0 lg:w-72">
            <Search
                class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
                v-model="searchInput"
                type="search"
                placeholder="Rechercher un article..."
                class="pr-9 pl-9"
            />
            <button
                v-if="searchInput"
                type="button"
                class="absolute top-1/2 right-3 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                @click="clearSearch"
            >
                <X class="h-4 w-4" />
            </button>
        </div>
    </div>
</template>
