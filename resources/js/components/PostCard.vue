<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { show } from '@/routes/posts';
import type { Post } from '@/types/post';
import { Link } from '@inertiajs/vue3';
import { Calendar, Eye, User } from 'lucide-vue-next';

interface Props {
    post: Post;
    showViewCount?: boolean;
    showStatus?: boolean;
}

withDefaults(defineProps<Props>(), {
    showViewCount: false,
    showStatus: false,
});

const formatDate = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
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
</script>

<template>
    <Card class="group transition-shadow hover:shadow-md">
        <Link :href="show(post).url" class="block">
            <!-- Featured Image -->
            <div
                v-if="post.featured_image_url"
                class="aspect-video w-full overflow-hidden rounded-t-xl"
            >
                <img
                    :src="post.featured_image_url"
                    :alt="post.title"
                    class="h-full w-full object-cover transition-transform group-hover:scale-105"
                />
            </div>

            <CardHeader class="pb-3">
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <!-- Status Badge -->
                    <span
                        v-if="showStatus"
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

                <CardTitle
                    class="line-clamp-2 text-lg transition-colors group-hover:text-primary"
                >
                    {{ post.title }}
                </CardTitle>

                <CardDescription class="line-clamp-2">
                    {{ post.excerpt }}
                </CardDescription>
            </CardHeader>

            <CardContent class="pt-0">
                <div
                    class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground"
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

                    <!-- View Count -->
                    <div v-if="showViewCount" class="flex items-center gap-1">
                        <Eye class="h-4 w-4" />
                        <span>{{ post.view_count }} vues</span>
                    </div>
                </div>
            </CardContent>
        </Link>
    </Card>
</template>
