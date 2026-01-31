<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card } from '@/components/ui/card';
import { show } from '@/routes/posts';
import type { Post } from '@/types/post';
import { Link } from '@inertiajs/vue3';
import { Calendar, Check, Eye, User } from 'lucide-vue-next';

interface Props {
    post: Post;
    showViewCount?: boolean;
    showStatus?: boolean;
    selectable?: boolean;
    selected?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showViewCount: false,
    showStatus: false,
    selectable: false,
    selected: false,
});

const emit = defineEmits<{
    select: [];
}>();

const handleSelect = () => {
    emit('select');
};

const formatDate = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const statusColors: Record<string, string> = {
    draft: 'bg-white/20 text-white',
    published: 'bg-green-500/30 text-white',
    scheduled: 'bg-blue-500/30 text-white',
    archived: 'bg-orange-500/30 text-white',
};

const statusLabels: Record<string, string> = {
    draft: 'Brouillon',
    published: 'Publié',
    scheduled: 'Programmé',
    archived: 'Archivé',
};

// Gradients for posts without images (same as carousel)
const gradients = [
    'from-violet-600 via-purple-600 to-indigo-700',
    'from-rose-500 via-pink-600 to-purple-700',
    'from-emerald-500 via-teal-600 to-cyan-700',
    'from-amber-500 via-orange-600 to-red-700',
    'from-blue-500 via-indigo-600 to-violet-700',
];

const getGradient = () => gradients[props.post.id % gradients.length];
</script>

<template>
    <Card class="group relative overflow-hidden transition-shadow hover:shadow-lg p-0">
        <!-- Selection Checkbox -->
        <label
            v-if="selectable"
            class="absolute right-3 top-3 z-20 flex h-6 w-6 cursor-pointer items-center justify-center rounded border shadow-sm backdrop-blur-sm transition-colors"
            :class="
                selected
                    ? 'border-white bg-white text-primary'
                    : 'border-white/50 bg-white/20 hover:bg-white/30'
            "
            @click.stop
        >
            <input
                type="checkbox"
                class="sr-only"
                :checked="selected"
                @change="handleSelect"
            />
            <Check v-if="selected" class="h-4 w-4" />
        </label>

        <Link :href="show(post).url" class="block h-full">
            <!-- Background: Image or Gradient -->
            <div class="aspect-[4/3] h-full relative w-full overflow-hidden">
                <template v-if="post.featured_image_url">
                    <img
                        :src="post.featured_image_url"
                        :alt="post.title"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <!-- Gradient Overlay for image -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"
                    />
                </template>
                <template v-else>
                    <!-- Gradient Background with pattern -->
                    <div
                        class="absolute inset-0 bg-gradient-to-br transition-all duration-500 group-hover:scale-105"
                        :class="getGradient()"
                    />
                    <!-- Decorative pattern overlay -->
                    <div
                        class="absolute inset-0 opacity-10"
                        style="
                            background-image: url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);
                        "
                    />
                    <!-- Subtle overlay for better text readability -->
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"
                    />
                </template>

                <!-- Content Overlay -->
                <div
                    class="absolute inset-0 flex flex-col justify-end p-4"
                >
                    <!-- Badges Row -->
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <!-- Status Badge -->
                        <span
                            v-if="showStatus"
                            :class="[
                                'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium backdrop-blur-sm',
                                statusColors[post.status],
                            ]"
                        >
                            <template v-if="post.status === 'scheduled' && post.published_at">
                                Programmé le {{ formatDate(post.published_at) }}
                            </template>
                            <template v-else>
                                {{ statusLabels[post.status] }}
                            </template>
                        </span>

                        <!-- Categories -->
                        <Badge
                            v-for="category in post.categories"
                            :key="category.id"
                            variant="secondary"
                            class="bg-white/20 text-white backdrop-blur-sm hover:bg-white/30"
                        >
                            {{ category.name }}
                        </Badge>
                    </div>

                    <!-- Title -->
                    <h3
                        class="mb-1 line-clamp-2 text-lg font-semibold text-white"
                    >
                        {{ post.title }}
                    </h3>

                    <!-- Excerpt -->
                    <p class="mb-3 line-clamp-2 text-sm text-white/80">
                        {{ post.excerpt }}
                    </p>

                    <!-- Meta -->
                    <div
                        class="flex flex-wrap items-center gap-3 text-xs text-white/70"
                    >
                        <!-- Author -->
                        <div
                            v-if="post.author"
                            class="flex items-center gap-1"
                        >
                            <User class="h-3.5 w-3.5" />
                            <span>{{ post.author.name }}</span>
                        </div>

                        <!-- Date -->
                        <div
                            v-if="post.published_at"
                            class="flex items-center gap-1"
                        >
                            <Calendar class="h-3.5 w-3.5" />
                            <span>{{ formatDate(post.published_at) }}</span>
                        </div>

                        <!-- View Count -->
                        <div
                            v-if="showViewCount"
                            class="flex items-center gap-1"
                        >
                            <Eye class="h-3.5 w-3.5" />
                            <span>{{ post.view_count }} vues</span>
                        </div>
                    </div>
                </div>
            </div>
        </Link>
    </Card>
</template>
