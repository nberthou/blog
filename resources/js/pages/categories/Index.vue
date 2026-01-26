<script setup lang="ts">
import { create, destroy, edit, index, show } from '@/routes/categories';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Category, PaginatedData } from '@/types/post';
import { Edit, Plus, Trash2 } from 'lucide-vue-next';

interface Props {
    categories: PaginatedData<Category>;
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth?.user;
const isAdmin = user?.id === 1;

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Catégories',
        href: index().url,
    },
];

const deleteDialogOpen = ref(false);
const categoryToDelete = ref<Category | null>(null);
const deleteForm = useForm({});

const openDeleteDialog = (category: Category) => {
    categoryToDelete.value = category;
    deleteDialogOpen.value = true;
};

const handleDelete = () => {
    if (categoryToDelete.value) {
        deleteForm.delete(destroy(categoryToDelete.value).url, {
            onSuccess: () => {
                deleteDialogOpen.value = false;
                categoryToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Catégories" />

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div
                class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">
                        Catégories
                    </h1>
                    <p class="mt-1 text-muted-foreground">
                        Gérez les catégories de votre blog
                    </p>
                </div>

                <Button v-if="isAdmin" as-child>
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Nouvelle catégorie
                    </Link>
                </Button>
            </div>

            <!-- Table -->
            <div
                v-if="categories.data.length > 0"
                class="overflow-hidden rounded-lg border"
            >
                <!-- Desktop Table -->
                <table class="hidden w-full md:table">
                    <thead class="bg-muted/50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Nom
                            </th>
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Description
                            </th>
                            <th
                                class="px-4 py-3 text-center text-sm font-medium text-muted-foreground"
                            >
                                Articles
                            </th>
                            <th
                                v-if="isAdmin"
                                class="px-4 py-3 text-right text-sm font-medium text-muted-foreground"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr
                            v-for="category in categories.data"
                            :key="category.id"
                            class="hover:bg-muted/30"
                        >
                            <td class="px-4 py-3">
                                <Link
                                    :href="show(category).url"
                                    class="font-medium hover:underline"
                                >
                                    {{ category.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-muted-foreground">
                                {{
                                    category.description || 'Aucune description'
                                }}
                            </td>
                            <td class="px-4 py-3 text-center text-sm">
                                {{ category.posts_count ?? 0 }}
                            </td>
                            <td v-if="isAdmin" class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        as-child
                                    >
                                        <Link :href="edit(category).url">
                                            <Edit class="h-4 w-4" />
                                            <span class="sr-only"
                                                >Modifier</span
                                            >
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="icon-sm"
                                        class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                        @click="openDeleteDialog(category)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                        <span class="sr-only">Supprimer</span>
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Mobile Cards -->
                <div class="divide-y md:hidden">
                    <div
                        v-for="category in categories.data"
                        :key="category.id"
                        class="p-4"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <Link
                                    :href="show(category).url"
                                    class="font-medium hover:underline"
                                >
                                    {{ category.name }}
                                </Link>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{
                                        category.description ||
                                        'Aucune description'
                                    }}
                                </p>
                                <p class="mt-2 text-sm">
                                    <span class="font-medium">{{
                                        category.posts_count ?? 0
                                    }}</span>
                                    article{{
                                        (category.posts_count ?? 0) > 1
                                            ? 's'
                                            : ''
                                    }}
                                </p>
                            </div>
                            <div v-if="isAdmin" class="ml-4 flex gap-1">
                                <Button variant="ghost" size="icon-sm" as-child>
                                    <Link :href="edit(category).url">
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="ghost"
                                    size="icon-sm"
                                    class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                                    @click="openDeleteDialog(category)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-16 text-center"
            >
                <p class="text-lg text-muted-foreground">
                    Aucune catégorie pour le moment.
                </p>
                <Button v-if="isAdmin" as-child class="mt-4">
                    <Link :href="create().url">
                        <Plus class="mr-2 h-4 w-4" />
                        Créer la première catégorie
                    </Link>
                </Button>
            </div>

            <!-- Pagination -->
            <div v-if="categories.data.length > 0" class="mt-8">
                <Pagination
                    :links="categories.links"
                    :prev-page-url="categories.prev_page_url"
                    :next-page-url="categories.next_page_url"
                    :from="categories.from"
                    :to="categories.to"
                    :total="categories.total"
                />
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
                <strong>{{ categoryToDelete?.name }}</strong> ? Cette action est
                irréversible.
            </template>
        </ConfirmDialog>
    </AppLayout>
</template>
