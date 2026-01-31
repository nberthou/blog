<script setup lang="ts">
import { home } from '@/routes';
import { index as categoriesIndex } from '@/routes/categories';
import { index as postsIndex } from '@/routes/posts';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const isAdmin = computed(() => page.props.auth?.user?.id === 1);

const currentYear = new Date().getFullYear();

const navLinks = computed(() => {
    const links = [
        { title: 'Accueil', href: home() },
        { title: 'Articles', href: postsIndex().url },
    ];

    if (isAdmin.value) {
        links.push({ title: 'Catégories', href: categoriesIndex().url });
    }

    return links;
});
</script>

<template>
    <footer class="border-t border-sidebar-border/80 bg-background">
        <div class="mx-auto max-w-7xl px-4 py-8 md:py-12">
            <div class="grid gap-8 md:grid-cols-2">
                <!-- Navigation -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold uppercase tracking-wider">
                        Navigation
                    </h4>
                    <nav class="flex flex-col space-y-2">
                        <Link
                            v-for="link in navLinks"
                            :key="link.title"
                            :href="link.href"
                            class="text-sm text-muted-foreground transition-colors hover:text-foreground"
                        >
                            {{ link.title }}
                        </Link>
                    </nav>
                </div>

                <!-- Tech Stack -->
                <div class="space-y-4">
                    <h4 class="text-sm font-semibold uppercase tracking-wider">
                        Construit avec
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        <span
                            class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400"
                        >
                            Laravel
                        </span>
                        <span
                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400"
                        >
                            Vue.js
                        </span>
                        <span
                            class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900/30 dark:text-purple-400"
                        >
                            Inertia
                        </span>
                        <span
                            class="inline-flex items-center rounded-full bg-cyan-100 px-2.5 py-0.5 text-xs font-medium text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400"
                        >
                            Tailwind CSS
                        </span>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div
                class="mt-8 border-t border-sidebar-border/80 pt-8 text-center"
            >
                <p class="text-sm text-muted-foreground">
                    &copy; {{ currentYear }} Tous droits réservés.
                </p>
            </div>
        </div>
    </footer>
</template>
