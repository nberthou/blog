<script setup lang="ts">
import { Button } from '@/components/ui/button';
import type { Comment } from '@/types/post';
import { Link, usePage } from '@inertiajs/vue3';
import { MessageSquare } from 'lucide-vue-next';
import { computed, defineAsyncComponent } from 'vue';

const CommentForm = defineAsyncComponent(() => import('./CommentForm.vue'));
const CommentItem = defineAsyncComponent(() => import('./CommentItem.vue'));

interface Props {
    comments: Comment[];
    postSlug: string;
    commentsCount: number;
}

const props = defineProps<Props>();

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const rootComments = computed(() =>
    props.comments.filter((comment) => comment.parent_id === null),
);
</script>

<template>
    <section class="mt-12 border-t pt-8">
        <!-- Header -->
        <div class="mb-6 flex items-center gap-2">
            <MessageSquare class="h-5 w-5" />
            <h2 class="text-xl font-semibold">
                Commentaires
                <span class="text-muted-foreground">({{ commentsCount }})</span>
            </h2>
        </div>

        <!-- Comment Form (for authenticated users) -->
        <div v-if="isAuthenticated" class="mb-8">
            <CommentForm :post-slug="postSlug" />
        </div>

        <!-- Login prompt (for guests) -->
        <div v-else class="mb-8 rounded-lg border bg-muted/50 p-4 text-center">
            <p class="text-sm text-muted-foreground">
                <Button variant="link" class="h-auto p-0" as-child>
                    <Link href="/login">Connectez-vous</Link>
                </Button>
                pour laisser un commentaire.
            </p>
        </div>

        <!-- Comments List -->
        <div v-if="rootComments.length" class="space-y-6">
            <CommentItem
                v-for="comment in rootComments"
                :key="comment.id"
                :comment="comment"
                :post-slug="postSlug"
            />
        </div>

        <!-- Empty State -->
        <div v-else class="py-8 text-center">
            <MessageSquare class="mx-auto h-12 w-12 text-muted-foreground/50" />
            <p class="mt-2 text-muted-foreground">
                Aucun commentaire pour le moment. Soyez le premier a commenter !
            </p>
        </div>
    </section>
</template>
