<script setup lang="ts">
import { destroy, edit, index } from '@/routes/posts';
import { Head, Link, useForm } from '@inertiajs/vue3';

import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { Post } from '@/types/post';
import { ArrowLeft, Calendar, Edit, Eye, Trash2, User } from 'lucide-vue-next';

interface Props {
    post: Post;
    canEdit: boolean;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: index().url,
    },
    {
        title: props.post.title,
        href: '#',
    },
];

const formatDate = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const deleteForm = useForm({});

const handleDelete = () => {
    deleteForm.delete(destroy(props.post).url);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="post.title" />

        <article class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Retour aux articles
                    </Link>
                </Button>
            </div>

            <!-- Featured Image -->
            <div
                v-if="post.featured_image_url"
                class="mb-8 aspect-video w-full overflow-hidden rounded-xl"
            >
                <img
                    :src="post.featured_image_url"
                    :alt="post.title"
                    class="h-full w-full object-cover"
                />
            </div>

            <!-- Header -->
            <header class="mb-8">
                <!-- Categories -->
                <div
                    v-if="post.categories?.length"
                    class="mb-4 flex flex-wrap gap-2"
                >
                    <Badge
                        v-for="category in post.categories"
                        :key="category.id"
                        variant="secondary"
                    >
                        {{ category.name }}
                    </Badge>
                </div>

                <!-- Title -->
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                    {{ post.title }}
                </h1>

                <!-- Meta -->
                <div
                    class="mt-4 flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
                >
                    <!-- Author -->
                    <div v-if="post.author" class="flex items-center gap-1">
                        <User class="h-4 w-4" />
                        <span>{{ post.author.name }}</span>
                    </div>

                    <!-- Date -->
                    <div
                        v-if="post.published_at"
                        class="flex items-center gap-1"
                    >
                        <Calendar class="h-4 w-4" />
                        <span>{{ formatDate(post.published_at) }}</span>
                    </div>

                    <!-- View Count (only for author) -->
                    <div v-if="canEdit" class="flex items-center gap-1">
                        <Eye class="h-4 w-4" />
                        <span>{{ post.view_count }} vues</span>
                    </div>
                </div>

                <!-- Actions (only for author) -->
                <div v-if="canEdit" class="mt-4 flex gap-2">
                    <Button variant="outline" size="sm" as-child>
                        <Link :href="edit(post).url">
                            <Edit class="mr-2 h-4 w-4" />
                            Modifier
                        </Link>
                    </Button>

                    <Dialog>
                        <DialogTrigger as-child>
                            <Button variant="destructive" size="sm">
                                <Trash2 class="mr-2 h-4 w-4" />
                                Supprimer
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Supprimer l'article ?</DialogTitle>
                                <DialogDescription>
                                    Cette action est irréversible. L'article
                                    sera définitivement supprimé.
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
                </div>
            </header>

            <!-- Excerpt -->
            <p class="mb-8 text-lg leading-relaxed text-muted-foreground">
                {{ post.excerpt }}
            </p>

            <!-- Content -->
            <div
                class="prose prose-lg dark:prose-invert max-w-none"
                v-html="post.content"
            />
        </article>
    </AppLayout>
</template>
