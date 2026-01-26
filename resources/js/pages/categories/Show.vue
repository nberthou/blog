<script setup lang="ts">
import { destroy, edit, index } from '@/routes/categories';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import ConfirmDialog from '@/components/ConfirmDialog.vue';
import PostCard from '@/components/PostCard.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/post';
import { ArrowLeft, Edit as EditIcon, Trash2 } from 'lucide-vue-next';

interface Props {
    category: Category;
}

const props = defineProps<Props>();

const page = usePage();
const user = page.props.auth?.user;
const isAdmin = user?.id === 1;

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Catégories',
        href: index().url,
    },
    {
        title: props.category.name,
        href: '#',
    },
];

const deleteDialogOpen = ref(false);
const deleteForm = useForm({});

const handleDelete = () => {
    deleteForm.delete(destroy(props.category).url, {
        onSuccess: () => {
            deleteDialogOpen.value = false;
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="category.name" />

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Retour aux catégories
                    </Link>
                </Button>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"
                >
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">
                            {{ category.name }}
                        </h1>
                        <p
                            v-if="category.description"
                            class="mt-2 text-muted-foreground"
                        >
                            {{ category.description }}
                        </p>
                        <p class="mt-2 text-sm text-muted-foreground">
                            {{ category.posts_count ?? 0 }} article{{
                                (category.posts_count ?? 0) > 1 ? 's' : ''
                            }}
                        </p>
                    </div>

                    <!-- Admin Actions -->
                    <div v-if="isAdmin" class="flex gap-2">
                        <Button variant="outline" size="sm" as-child>
                            <Link :href="edit(category).url">
                                <EditIcon class="mr-2 h-4 w-4" />
                                Modifier
                            </Link>
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            @click="deleteDialogOpen = true"
                        >
                            <Trash2 class="mr-2 h-4 w-4" />
                            Supprimer
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            <div
                v-if="category.posts && category.posts.length > 0"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <PostCard
                    v-for="post in category.posts"
                    :key="post.id"
                    :post="post"
                    :show-status="user?.id === post.user_id"
                    :show-view-count="user?.id === post.user_id"
                />
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <p class="text-lg text-muted-foreground">
                    Aucun article dans cette catégorie pour le moment.
                </p>
            </div>
        </div>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            v-model:open="deleteDialogOpen"
            title="Supprimer la catégorie ?"
            confirm-label="Supprimer"
            :loading="deleteForm.processing"
            @confirm="handleDelete"
        >
            <template #description>
                Voulez-vous vraiment supprimer la catégorie
                <strong>{{ category.name }}</strong> ? Cette action est
                irréversible.
            </template>
        </ConfirmDialog>
    </AppLayout>
</template>
