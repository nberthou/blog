<script setup lang="ts">
import { create, destroy, edit, myPosts, show } from '@/routes/posts';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { PaginatedData, Post, PostStatus } from '@/types/post';
import {
    Calendar,
    Edit,
    Eye,
    MoreVertical,
    PenLine,
    Trash2,
} from 'lucide-vue-next';

interface Props {
    posts: PaginatedData<Post>;
    statuses: PostStatus[];
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Mes articles',
        href: myPosts().url,
    },
];

const formatDate = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const statusColors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
    published:
        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    scheduled: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    archived:
        'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
};

const statusLabels: Record<string, string> = {
    draft: 'Brouillon',
    published: 'Publié',
    scheduled: 'Programmé',
    archived: 'Archivé',
};

const postToDelete = ref<Post | null>(null);
const deleteDialogOpen = ref(false);
const deleteForm = useForm({});

const openDeleteDialog = (post: Post) => {
    postToDelete.value = post;
    deleteDialogOpen.value = true;
};

const handleDelete = () => {
    if (postToDelete.value) {
        deleteForm.delete(destroy(postToDelete.value).url, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                postToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Mes articles" />

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div
                class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Mes articles
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Gérez vos articles de blog
                    </p>
                </div>

                <Button as-child>
                    <Link :href="create().url">
                        <PenLine class="mr-2 h-4 w-4" />
                        Nouvel article
                    </Link>
                </Button>
            </div>

            <!-- Posts List -->
            <div v-if="posts.data.length > 0" class="space-y-4">
                <Card v-for="post in posts.data" :key="post.id">
                    <div class="flex flex-col sm:flex-row">
                        <!-- Featured Image Thumbnail -->
                        <div
                            v-if="post.featured_image_url"
                            class="w-full flex-shrink-0 sm:w-48"
                        >
                            <Link :href="show(post).url">
                                <img
                                    :src="post.featured_image_url"
                                    :alt="post.title"
                                    class="h-32 w-full rounded-t-lg object-cover sm:h-full sm:rounded-l-lg sm:rounded-tr-none"
                                />
                            </Link>
                        </div>

                        <div class="min-w-0 flex-1">
                            <CardHeader class="pb-2">
                                <div
                                    class="flex items-start justify-between gap-2"
                                >
                                    <div class="min-w-0 flex-1">
                                        <div
                                            class="mb-2 flex flex-wrap items-center gap-2"
                                        >
                                            <!-- Status -->
                                            <span
                                                :class="[
                                                    'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                                    statusColors[post.status],
                                                ]"
                                            >
                                                {{ statusLabels[post.status] }}
                                            </span>

                                            <!-- Categories -->
                                            <Badge
                                                v-for="category in post.categories"
                                                :key="category.id"
                                                variant="secondary"
                                            >
                                                {{ category.name }}
                                            </Badge>
                                        </div>

                                        <CardTitle class="line-clamp-1">
                                            <Link
                                                :href="show(post).url"
                                                class="transition-colors hover:text-primary"
                                            >
                                                {{ post.title }}
                                            </Link>
                                        </CardTitle>

                                        <CardDescription
                                            class="mt-1 line-clamp-2"
                                        >
                                            {{ post.excerpt }}
                                        </CardDescription>
                                    </div>

                                    <!-- Actions Dropdown -->
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon-sm"
                                            >
                                                <MoreVertical class="h-4 w-4" />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuItem as-child>
                                                <Link :href="show(post).url">
                                                    <Eye class="mr-2 h-4 w-4" />
                                                    Voir
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link :href="edit(post).url">
                                                    <Edit
                                                        class="mr-2 h-4 w-4"
                                                    />
                                                    Modifier
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                class="text-destructive focus:text-destructive"
                                                @click="openDeleteDialog(post)"
                                            >
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                Supprimer
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </CardHeader>

                            <CardContent class="pt-0">
                                <div
                                    class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
                                >
                                    <!-- Date -->
                                    <div class="flex items-center gap-1">
                                        <Calendar class="h-4 w-4" />
                                        <span>
                                            {{
                                                post.published_at
                                                    ? formatDate(
                                                          post.published_at,
                                                      )
                                                    : formatDate(
                                                          post.created_at,
                                                      )
                                            }}
                                        </span>
                                    </div>

                                    <!-- View Count -->
                                    <div class="flex items-center gap-1">
                                        <Eye class="h-4 w-4" />
                                        <span>{{ post.view_count }} vues</span>
                                    </div>
                                </div>
                            </CardContent>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <p class="text-lg text-muted-foreground">
                    Vous n'avez pas encore écrit d'article.
                </p>
                <Button as-child class="mt-4">
                    <Link :href="create().url">
                        <PenLine class="mr-2 h-4 w-4" />
                        Écrire votre premier article
                    </Link>
                </Button>
            </div>

            <!-- Pagination -->
            <div v-if="posts.data.length > 0" class="mt-8">
                <Pagination
                    :links="posts.links"
                    :prev-page-url="posts.prev_page_url"
                    :next-page-url="posts.next_page_url"
                    :from="posts.from"
                    :to="posts.to"
                    :total="posts.total"
                />
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <Dialog v-model:open="deleteDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Supprimer l'article ?</DialogTitle>
                    <DialogDescription>
                        Êtes-vous sûr de vouloir supprimer "{{
                            postToDelete?.title
                        }}" ? Cette action est irréversible.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary">Annuler</Button>
                    </DialogClose>
                    <Button
                        variant="destructive"
                        :disabled="deleteForm.processing"
                        @click="handleDelete"
                    >
                        Supprimer
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
