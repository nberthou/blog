<script setup lang="ts">
import CategoryTabs from '@/components/CategoryTabs.vue';
import FeaturedCarousel from '@/components/FeaturedCarousel.vue';
import PostCard from '@/components/PostCard.vue';
import { Button } from '@/components/ui/button';
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { Category, PaginatedData, Post } from '@/types/post';
import { Head, Link } from '@inertiajs/vue3';
import { FileText, Search } from 'lucide-vue-next';

interface Props {
    featuredPosts: Post[];
    posts: PaginatedData<Post>;
    categories: Category[];
    filters: {
        category: string | null;
        search: string | null;
    };
}

defineProps<Props>();
</script>

<template>
    <Head title="Accueil">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <AppHeaderLayout>
        <div class="mx-auto max-w-7xl space-y-8 px-4 py-6 sm:px-6 lg:px-8">
            <!-- Featured Carousel -->
            <section v-if="featuredPosts.length > 0">
                <FeaturedCarousel :posts="featuredPosts" />
            </section>

            <!-- Categories + Search -->
            <section>
                <CategoryTabs :categories="categories" :filters="filters" />
            </section>

            <!-- Posts Grid -->
            <section>
                <!-- Results info -->
                <div
                    v-if="filters.category || filters.search"
                    class="mb-4 text-sm text-muted-foreground"
                >
                    <span v-if="posts.total > 0">
                        {{ posts.total }} article{{
                            posts.total > 1 ? 's' : ''
                        }}
                        trouve{{ posts.total > 1 ? 's' : '' }}
                    </span>
                    <span v-if="filters.category">
                        dans la categorie
                        <strong class="font-medium text-foreground">{{
                            categories.find((c) => c.slug === filters.category)
                                ?.name
                        }}</strong>
                    </span>
                    <span v-if="filters.search">
                        pour "
                        <strong class="font-medium text-foreground">{{
                            filters.search
                        }}</strong>
                        "
                    </span>
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
                    class="flex flex-col items-center justify-center rounded-lg border border-dashed py-16 text-center"
                >
                    <div class="mb-4 rounded-full bg-muted p-4">
                        <component
                            :is="filters.search ? Search : FileText"
                            class="h-8 w-8 text-muted-foreground"
                        />
                    </div>
                    <h3 class="mb-2 text-lg font-semibold">
                        {{
                            filters.search ? 'Aucun resultat' : 'Aucun article'
                        }}
                    </h3>
                    <p class="mb-4 max-w-sm text-sm text-muted-foreground">
                        {{
                            filters.search
                                ? `Aucun article ne correspond a votre recherche "${filters.search}".`
                                : filters.category
                                  ? 'Aucun article dans cette categorie pour le moment.'
                                  : 'Aucun article publie pour le moment.'
                        }}
                    </p>
                    <Button
                        v-if="filters.search || filters.category"
                        variant="outline"
                        as-child
                    >
                        <Link href="/">Voir tous les articles</Link>
                    </Button>
                </div>
            </section>

            <!-- Pagination -->
            <nav
                v-if="posts.last_page > 1"
                class="flex items-center justify-center gap-1"
            >
                <template v-for="link in posts.links" :key="link.label">
                    <Button
                        v-if="link.url"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        as-child
                    >
                        <Link :href="link.url" preserve-scroll>
                            <span v-html="link.label" />
                        </Link>
                    </Button>
                    <Button
                        v-else
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        disabled
                    >
                        <span v-html="link.label" />
                    </Button>
                </template>
            </nav>
        </div>
    </AppHeaderLayout>
</template>
