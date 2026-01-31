<script setup lang="ts">
import { batchDestroy, create, index } from '@/routes/posts';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, reactive, ref } from 'vue';

import PostCard from '@/components/PostCard.vue';
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
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { PaginatedData, Post } from '@/types/post';
import { Check, Minus, PenLine, Trash2 } from 'lucide-vue-next';

interface Props {
    posts: PaginatedData<Post>;
}

const props = defineProps<Props>();

const page = usePage();
const user = page.props.auth?.user;

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: index().url,
    },
];

// Lazy loading state
const allPosts = ref<Post[]>([...props.posts.data]);
console.log('allPosts', allPosts.value);
const currentPage = ref(props.posts.current_page);
const lastPage = ref(props.posts.last_page);
const isLoading = ref(false);
const loadMoreTrigger = ref<HTMLElement | null>(null);

const hasMorePages = computed(() => currentPage.value < lastPage.value);

const loadMore = async () => {
    if (isLoading.value || !hasMorePages.value) return;

    isLoading.value = true;
    const nextPage = currentPage.value + 1;

    try {
        const response = await fetch(`${index().url}?page=${nextPage}`, {
            headers: {
                'X-Inertia': 'true',
                'X-Inertia-Version': page.version ?? '',
                'X-Inertia-Partial-Data': 'posts',
                'X-Inertia-Partial-Component': 'posts/Index',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) throw new Error('Failed to load posts');

        const data = await response.json();
        const newPosts = (data.props.posts as PaginatedData<Post>).data;
        allPosts.value.push(...newPosts);
        currentPage.value = nextPage;
        lastPage.value = data.props.posts.last_page;
    } catch (error) {
        console.error('Error loading more posts:', error);
    } finally {
        isLoading.value = false;
    }
};

// Intersection Observer for infinite scroll
let observer: IntersectionObserver | null = null;

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && hasMorePages.value) {
                loadMore();
            }
        },
        { rootMargin: '100px' },
    );

    if (loadMoreTrigger.value) {
        observer.observe(loadMoreTrigger.value);
    }
});

onUnmounted(() => {
    observer?.disconnect();
});

// Get only user's posts (for selection)
const userPosts = computed(() =>
    allPosts.value.filter((post) => post.user_id === user?.id),
);

// Multi-select and batch delete
const selection = reactive<Record<number, boolean>>({});
const batchDeleteDialogOpen = ref(false);
const batchDeleteForm = useForm<{ ids: number[] }>({ ids: [] });

const selectedIds = computed(() =>
    Object.entries(selection)
        .filter(([, selected]) => selected)
        .map(([id]) => Number(id)),
);

const selectedCount = computed(() => selectedIds.value.length);

const isAllUserPostsSelected = computed(() => {
    return (
        userPosts.value.length > 0 &&
        userPosts.value.every((post) => selection[post.id])
    );
});

const isSomeSelected = computed(() => {
    return (
        selectedCount.value > 0 &&
        !isAllUserPostsSelected.value &&
        userPosts.value.some((post) => selection[post.id])
    );
});

const toggleSelectAll = () => {
    const newValue = !isAllUserPostsSelected.value;
    userPosts.value.forEach((post) => {
        selection[post.id] = newValue;
    });
};

const toggleSelect = (postId: number) => {
    selection[postId] = !selection[postId];
};

const openBatchDeleteDialog = () => {
    batchDeleteDialogOpen.value = true;
};

const handleBatchDelete = () => {
    batchDeleteForm.ids = [...selectedIds.value];
    batchDeleteForm.delete(batchDestroy().url, {
        onSuccess: () => {
            batchDeleteDialogOpen.value = false;
            // Remove deleted posts from allPosts
            allPosts.value = allPosts.value.filter(
                (post) => !selectedIds.value.includes(post.id),
            );
            Object.keys(selection).forEach((key) => {
                delete selection[Number(key)];
            });
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Articles" />

        <div class="px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div
                class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Articles</h1>
                    <p class="mt-1 text-muted-foreground">
                        Découvrez nos derniers articles
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Button
                        v-if="selectedCount > 0"
                        variant="destructive"
                        @click="openBatchDeleteDialog"
                    >
                        <Trash2 class="mr-2 h-4 w-4" />
                        Supprimer ({{ selectedCount }})
                    </Button>
                    <Button v-if="user" as-child>
                        <Link :href="create().url">
                            <PenLine class="mr-2 h-4 w-4" />
                            Nouvel article
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Select All (only if user has posts) -->
            <label
                v-if="userPosts.length > 0 && allPosts.length > 0"
                class="mb-6 flex cursor-pointer items-center gap-3"
            >
                <span
                    class="flex h-4 w-4 items-center justify-center rounded border shadow-sm transition-colors"
                    :class="
                        isAllUserPostsSelected || isSomeSelected
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'border-input bg-background'
                    "
                >
                    <input
                        type="checkbox"
                        class="sr-only"
                        :checked="isAllUserPostsSelected"
                        @change="toggleSelectAll"
                    />
                    <Check v-if="isAllUserPostsSelected" class="h-3 w-3" />
                    <Minus v-else-if="isSomeSelected" class="h-3 w-3" />
                </span>
                <span class="text-sm text-muted-foreground">
                    Sélectionner tous mes articles ({{ userPosts.length }})
                </span>
            </label>

            <!-- Posts Grid -->
            <div v-if="allPosts.length > 0" class="grid gap-8 sm:grid-cols-2">
                <PostCard
                    v-for="post in allPosts"
                    :key="post.id"
                    :post="post"
                    :show-status="user?.id === post.user_id"
                    :show-view-count="user?.id === post.user_id"
                    :selectable="user?.id === post.user_id"
                    :selected="selection[post.id] ?? false"
                    @select="toggleSelect(post.id)"
                />
            </div>

            <!-- Load More Trigger -->
            <div
                ref="loadMoreTrigger"
                class="mt-8 flex items-center justify-center py-4"
            >
                <Spinner v-if="isLoading" class="h-6 w-6" />
                <p
                    v-else-if="!hasMorePages && allPosts.length > 0"
                    class="text-sm text-muted-foreground"
                >
                    Tous les articles ont été chargés
                </p>
            </div>

            <!-- Empty State -->
            <div
                v-if="allPosts.length === 0"
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <p class="text-lg text-muted-foreground">
                    Aucun article publié pour le moment.
                </p>
                <Button v-if="user" as-child class="mt-4">
                    <Link :href="create().url">
                        <PenLine class="mr-2 h-4 w-4" />
                        Écrire le premier article
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Batch Delete Confirmation Dialog -->
        <Dialog v-model:open="batchDeleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer les articles ?</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer
                        {{ selectedCount }}
                        {{ selectedCount > 1 ? 'articles' : 'article' }} ? Cette
                        action est irréversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">Annuler</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        :disabled="batchDeleteForm.processing"
                        @click="handleBatchDelete"
                    >
                        Supprimer ({{ selectedCount }})
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
