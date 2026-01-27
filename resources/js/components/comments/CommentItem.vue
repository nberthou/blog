<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import { useInitials } from '@/composables/useInitials';
import { useRelativeTime } from '@/composables/useRelativeTime';
import type { Comment } from '@/types/post';
import { useForm, usePage } from '@inertiajs/vue3';
import { Check, Edit, MessageSquare, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import CommentForm from './CommentForm.vue';

interface Props {
    comment: Comment;
    postSlug: string;
    depth?: number;
}

const props = withDefaults(defineProps<Props>(), {
    depth: 0,
});

const page = usePage();
const { getInitials } = useInitials();
const { formatRelativeTime } = useRelativeTime();

const currentUser = computed(() => page.props.auth?.user);
const isAdmin = computed(() => currentUser.value?.id === 1);
const isAuthor = computed(
    () => currentUser.value?.id === props.comment.user_id,
);
const canModify = computed(() => isAuthor.value || isAdmin.value);
const canReply = computed(() => currentUser.value && props.depth < 1);

const showReplyForm = ref(false);
const isEditing = ref(false);
const showDeleteDialog = ref(false);

const editForm = useForm({
    content: props.comment.content,
});

const deleteForm = useForm({});

const updateUrl = computed(() => `/comments/${props.comment.id}`);
const deleteUrl = computed(() => `/comments/${props.comment.id}`);

const toggleReply = () => {
    showReplyForm.value = !showReplyForm.value;
    isEditing.value = false;
};

const startEditing = () => {
    editForm.content = props.comment.content;
    isEditing.value = true;
    showReplyForm.value = false;
};

const cancelEditing = () => {
    isEditing.value = false;
    editForm.reset();
};

const submitEdit = () => {
    editForm.put(updateUrl.value, {
        preserveScroll: true,
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const handleDelete = () => {
    deleteForm.delete(deleteUrl.value, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
        },
    });
};

const onReplySubmitted = () => {
    showReplyForm.value = false;
};
</script>

<template>
    <div
        class="flex gap-3"
        :class="{ 'ml-8 border-l-2 border-muted pl-4': depth > 0 }"
    >
        <Avatar class="h-8 w-8 shrink-0">
            <AvatarFallback class="text-xs">
                {{ getInitials(comment.author.name) }}
            </AvatarFallback>
        </Avatar>

        <div class="min-w-0 flex-1 space-y-2">
            <!-- Header -->
            <div class="flex items-center gap-2 text-sm">
                <span class="font-medium">{{ comment.author.name }}</span>
                <span class="text-muted-foreground">{{
                    formatRelativeTime(comment.created_at)
                }}</span>
                <span
                    v-if="comment.created_at !== comment.updated_at"
                    class="text-xs text-muted-foreground"
                >
                    (modifie)
                </span>
            </div>

            <!-- Content or Edit Form -->
            <div v-if="isEditing" class="space-y-2">
                <Textarea
                    v-model="editForm.content"
                    rows="3"
                    class="resize-none"
                    :class="{ 'border-destructive': editForm.errors.content }"
                />
                <p
                    v-if="editForm.errors.content"
                    class="text-sm text-destructive"
                >
                    {{ editForm.errors.content }}
                </p>
                <div class="flex gap-2">
                    <Button size="sm" variant="outline" @click="cancelEditing">
                        <X class="mr-1 h-3 w-3" />
                        Annuler
                    </Button>
                    <Button
                        size="sm"
                        :disabled="editForm.processing"
                        @click="submitEdit"
                    >
                        <Check class="mr-1 h-3 w-3" />
                        Enregistrer
                    </Button>
                </div>
            </div>
            <p v-else class="text-sm whitespace-pre-wrap">
                {{ comment.content }}
            </p>

            <!-- Actions -->
            <div v-if="!isEditing" class="flex items-center gap-2">
                <Button
                    v-if="canReply"
                    variant="ghost"
                    size="sm"
                    class="h-7 px-2 text-xs"
                    @click="toggleReply"
                >
                    <MessageSquare class="mr-1 h-3 w-3" />
                    {{ showReplyForm ? 'Annuler' : 'Repondre' }}
                </Button>
                <Button
                    v-if="canModify"
                    variant="ghost"
                    size="sm"
                    class="h-7 px-2 text-xs"
                    @click="startEditing"
                >
                    <Edit class="mr-1 h-3 w-3" />
                    Modifier
                </Button>
                <Button
                    v-if="canModify"
                    variant="ghost"
                    size="sm"
                    class="h-7 px-2 text-xs text-destructive hover:text-destructive"
                    @click="showDeleteDialog = true"
                >
                    <Trash2 class="mr-1 h-3 w-3" />
                    Supprimer
                </Button>
            </div>

            <!-- Reply Form -->
            <div v-if="showReplyForm" class="mt-3">
                <CommentForm
                    :post-slug="postSlug"
                    :parent-id="comment.id"
                    placeholder="Votre reponse..."
                    button-label="Repondre"
                    autofocus
                    @submitted="onReplySubmitted"
                />
            </div>

            <!-- Replies -->
            <div v-if="comment.replies?.length" class="mt-4 space-y-4">
                <CommentItem
                    v-for="reply in comment.replies"
                    :key="reply.id"
                    :comment="reply"
                    :post-slug="postSlug"
                    :depth="depth + 1"
                />
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Dialog -->
    <ConfirmDialog
        v-model:open="showDeleteDialog"
        title="Supprimer le commentaire ?"
        description="Cette action est irreversible. Le commentaire et toutes ses reponses seront definitivement supprimes."
        confirm-label="Supprimer"
        :loading="deleteForm.processing"
        @confirm="handleDelete"
    />
</template>
