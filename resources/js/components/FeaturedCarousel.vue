<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
    type CarouselApi,
} from '@/components/ui/carousel';
import { show } from '@/routes/posts';
import type { Post } from '@/types/post';
import { Link } from '@inertiajs/vue3';
import Autoplay from 'embla-carousel-autoplay';
import { Calendar, User } from 'lucide-vue-next';
import { ref, watchEffect } from 'vue';

interface Props {
    posts: Post[];
}

defineProps<Props>();

const carouselApi = ref<CarouselApi>();
const current = ref(0);

const autoplayPlugin = Autoplay({
    delay: 5000,
    stopOnInteraction: false,
    stopOnMouseEnter: true,
});

watchEffect(() => {
    if (!carouselApi.value) return;

    current.value = carouselApi.value.selectedScrollSnap();

    carouselApi.value.on('select', () => {
        current.value = carouselApi.value!.selectedScrollSnap();
    });
});

const formatDate = (date: string | null) => {
    if (!date) return '';
    return new Date(date).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Gradients for posts without images
const gradients = [
    'from-violet-600 via-purple-600 to-indigo-700',
    'from-rose-500 via-pink-600 to-purple-700',
    'from-emerald-500 via-teal-600 to-cyan-700',
    'from-amber-500 via-orange-600 to-red-700',
    'from-blue-500 via-indigo-600 to-violet-700',
];

const getGradient = (index: number) => gradients[index % gradients.length];
</script>

<template>
    <div class="relative">
        <Carousel
            class="w-full"
            :plugins="[autoplayPlugin]"
            :opts="{ loop: true }"
            @init-api="(api) => (carouselApi = api)"
        >
            <CarouselContent>
                <CarouselItem v-for="(post, index) in posts" :key="post.id">
                    <Link
                        :href="show(post).url"
                        class="group relative block aspect-[21/9] overflow-hidden rounded-xl"
                    >
                        <!-- Background: Image or Gradient -->
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
                                :class="getGradient(index)"
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

                        <!-- Content -->
                        <div
                            class="absolute inset-0 flex flex-col justify-end p-6 md:p-8 lg:p-10"
                        >
                            <!-- Categories -->
                            <div class="mb-3 flex flex-wrap gap-2">
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
                            <h2
                                class="mb-2 line-clamp-2 text-xl font-bold text-white md:text-2xl lg:text-3xl"
                            >
                                {{ post.title }}
                            </h2>

                            <!-- Excerpt -->
                            <p
                                class="mb-4 line-clamp-2 text-sm text-white/80 md:text-base"
                            >
                                {{ post.excerpt }}
                            </p>

                            <!-- Meta -->
                            <div
                                class="flex flex-wrap items-center gap-4 text-sm text-white/70"
                            >
                                <div
                                    v-if="post.author"
                                    class="flex items-center gap-1.5"
                                >
                                    <User class="h-4 w-4" />
                                    <span>{{ post.author.name }}</span>
                                </div>
                                <div
                                    v-if="post.published_at"
                                    class="flex items-center gap-1.5"
                                >
                                    <Calendar class="h-4 w-4" />
                                    <span>{{
                                        formatDate(post.published_at)
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </Link>
                </CarouselItem>
            </CarouselContent>

            <!-- Navigation Arrows -->
            <CarouselPrevious
                class="left-4 h-10 w-10 border-0 bg-white/20 text-white backdrop-blur-sm hover:bg-white/30"
            />
            <CarouselNext
                class="right-4 h-10 w-10 border-0 bg-white/20 text-white backdrop-blur-sm hover:bg-white/30"
            />
        </Carousel>

        <!-- Dots Indicator -->
        <div class="mt-4 flex justify-center gap-2">
            <button
                v-for="(_, index) in posts"
                :key="index"
                class="h-2 w-2 rounded-full transition-all"
                :class="[
                    current === index
                        ? 'w-6 bg-primary'
                        : 'bg-muted-foreground/30 hover:bg-muted-foreground/50',
                ]"
                @click="carouselApi?.scrollTo(index)"
            />
        </div>
    </div>
</template>
