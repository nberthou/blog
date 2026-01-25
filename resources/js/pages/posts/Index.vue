<script setup lang="ts">
import { create, index } from '@/routes/posts';
import { Head, Link, usePage } from '@inertiajs/vue3';

import Pagination from '@/components/Pagination.vue';
import PostCard from '@/components/PostCard.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { PaginatedData, Post } from '@/types/post';
import { PenLine } from 'lucide-vue-next';

interface Props {
    posts: PaginatedData<Post>;
}

defineProps<Props>();

const page = usePage();
const user = page.props.auth?.user;

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: index().url,
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Articles" />

        <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
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

                <Button v-if="user" as-child>
                    <Link :href="create().url">
                        <PenLine class="mr-2 h-4 w-4" />
                        Nouvel article
                    </Link>
                </Button>
            </div>

            <!-- Posts Grid -->
            <div
                v-if="posts.data.length > 0"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <PostCard
                    v-for="post in posts.data"
                    :key="post.id"
                    :post="post"
                />
            </div>

            <!-- Empty State -->
            <div
                v-else
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
    </AppLayout>
</template>
