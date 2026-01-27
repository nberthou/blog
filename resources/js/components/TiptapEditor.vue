<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import Link from '@tiptap/extension-link';
import Placeholder from '@tiptap/extension-placeholder';
import StarterKit from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';
import {
    Bold,
    Heading1,
    Heading2,
    Heading3,
    Italic,
    Link as LinkIcon,
    List,
    ListOrdered,
    Minus,
    Quote,
    Redo,
    Strikethrough,
    Undo,
    Unlink,
} from 'lucide-vue-next';
import { watch } from 'vue';

interface Props {
    modelValue?: string;
    placeholder?: string;
    class?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    placeholder: 'Commencez à écrire...',
    disabled: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue,
    editable: !props.disabled,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3],
            },
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-primary underline',
            },
        }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

watch(
    () => props.modelValue,
    (value) => {
        if (editor.value && editor.value.getHTML() !== value) {
            editor.value.commands.setContent(value || '', false);
        }
    },
);

watch(
    () => props.disabled,
    (disabled) => {
        editor.value?.setEditable(!disabled);
    },
);

const setLink = () => {
    const previousUrl = editor.value?.getAttributes('link').href;
    const url = window.prompt('URL du lien', previousUrl);

    if (url === null) return;

    if (url === '') {
        editor.value?.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }

    editor.value
        ?.chain()
        .focus()
        .extendMarkRange('link')
        .setLink({ href: url })
        .run();
};
</script>

<template>
    <div
        :class="
            cn(
                'rounded-md border border-input bg-background',
                props.disabled && 'cursor-not-allowed opacity-50',
                props.class,
            )
        "
    >
        <!-- Toolbar -->
        <div
            v-if="editor"
            class="flex flex-wrap gap-1 border-b border-input p-2"
        >
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{
                    'bg-muted': editor.isActive('heading', { level: 1 }),
                }"
                @click="
                    editor.chain().focus().toggleHeading({ level: 1 }).run()
                "
                :disabled="disabled"
            >
                <Heading1 class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{
                    'bg-muted': editor.isActive('heading', { level: 2 }),
                }"
                @click="
                    editor.chain().focus().toggleHeading({ level: 2 }).run()
                "
                :disabled="disabled"
            >
                <Heading2 class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{
                    'bg-muted': editor.isActive('heading', { level: 3 }),
                }"
                @click="
                    editor.chain().focus().toggleHeading({ level: 3 }).run()
                "
                :disabled="disabled"
            >
                <Heading3 class="h-4 w-4" />
            </Button>

            <div class="mx-1 h-6 w-px bg-border" />

            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('bold') }"
                @click="editor.chain().focus().toggleBold().run()"
                :disabled="disabled"
            >
                <Bold class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('italic') }"
                @click="editor.chain().focus().toggleItalic().run()"
                :disabled="disabled"
            >
                <Italic class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('strike') }"
                @click="editor.chain().focus().toggleStrike().run()"
                :disabled="disabled"
            >
                <Strikethrough class="h-4 w-4" />
            </Button>

            <div class="mx-1 h-6 w-px bg-border" />

            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('bulletList') }"
                @click="editor.chain().focus().toggleBulletList().run()"
                :disabled="disabled"
            >
                <List class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('orderedList') }"
                @click="editor.chain().focus().toggleOrderedList().run()"
                :disabled="disabled"
            >
                <ListOrdered class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('blockquote') }"
                @click="editor.chain().focus().toggleBlockquote().run()"
                :disabled="disabled"
            >
                <Quote class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                @click="editor.chain().focus().setHorizontalRule().run()"
                :disabled="disabled"
            >
                <Minus class="h-4 w-4" />
            </Button>

            <div class="mx-1 h-6 w-px bg-border" />

            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                :class="{ 'bg-muted': editor.isActive('link') }"
                @click="setLink"
                :disabled="disabled"
            >
                <LinkIcon class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                @click="editor.chain().focus().unsetLink().run()"
                :disabled="!editor.isActive('link') || disabled"
            >
                <Unlink class="h-4 w-4" />
            </Button>

            <div class="mx-1 h-6 w-px bg-border" />

            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                @click="editor.chain().focus().undo().run()"
                :disabled="!editor.can().undo() || disabled"
            >
                <Undo class="h-4 w-4" />
            </Button>
            <Button
                type="button"
                variant="ghost"
                size="icon-sm"
                @click="editor.chain().focus().redo().run()"
                :disabled="!editor.can().redo() || disabled"
            >
                <Redo class="h-4 w-4" />
            </Button>
        </div>

        <!-- Editor Content -->
        <EditorContent
            :editor="editor"
            class="prose prose-sm min-h-[200px] max-w-none p-4 focus-within:outline-none dark:prose-invert [&_.is-editor-empty:first-child::before]:pointer-events-none [&_.is-editor-empty:first-child::before]:float-left [&_.is-editor-empty:first-child::before]:h-0 [&_.is-editor-empty:first-child::before]:text-muted-foreground [&_.is-editor-empty:first-child::before]:content-[attr(data-placeholder)] [&_.tiptap]:min-h-[180px] [&_.tiptap]:outline-none"
        />
    </div>
</template>
