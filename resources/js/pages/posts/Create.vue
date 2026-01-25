<script setup lang="ts">
import { index, store } from '@/routes/posts';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import InputError from '@/components/InputError.vue';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { PostStatus } from '@/types/post';
import { ArrowLeft, ImagePlus, X } from 'lucide-vue-next';

interface Props {
    statuses: PostStatus[];
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Articles',
        href: index().url,
    },
    {
        title: 'Nouvel article',
        href: '#',
    },
];

const form = useForm({
    title: '',
    excerpt: '',
    content: '',
    status: 'draft',
    published_at: '',
    featured_image: null as File | null,
});

const imagePreview = ref<string | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.featured_image = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.featured_image = null;
    imagePreview.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const submit = () => {
    form.post(store().url, {
        forceFormData: true,
        preserveScroll: false,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Nouvel article" />

        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Retour aux articles
                    </Link>
                </Button>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight">
                    Nouvel article
                </h1>
                <p class="mt-1 text-muted-foreground">
                    Créez un nouvel article pour votre blog
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Title -->
                <div class="space-y-2">
                    <Label for="title">Titre *</Label>
                    <Input
                        id="title"
                        v-model="form.title"
                        placeholder="Le titre de votre article"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <!-- Excerpt -->
                <div class="space-y-2">
                    <Label for="excerpt">Extrait *</Label>
                    <Textarea
                        id="excerpt"
                        v-model="form.excerpt"
                        placeholder="Un court résumé de votre article (affiché dans la liste)"
                        rows="3"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.excerpt" />
                </div>

                <!-- Content -->
                <div class="space-y-2">
                    <Label>Contenu *</Label>
                    <TiptapEditor
                        v-model="form.content"
                        placeholder="Le contenu de votre article..."
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.content" />
                </div>

                <!-- Featured Image -->
                <div class="space-y-2">
                    <Label>Image à la une</Label>
                    <div v-if="imagePreview" class="relative w-full max-w-md">
                        <img
                            :src="imagePreview"
                            alt="Aperçu"
                            class="aspect-video w-full rounded-lg object-cover"
                        />
                        <Button
                            type="button"
                            variant="destructive"
                            size="icon-sm"
                            class="absolute top-2 right-2"
                            @click="removeImage"
                        >
                            <X class="h-4 w-4" />
                        </Button>
                    </div>
                    <div v-else>
                        <label
                            class="flex aspect-video w-full max-w-md cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-muted-foreground/25 bg-muted/50 transition-colors hover:bg-muted"
                        >
                            <ImagePlus
                                class="h-10 w-10 text-muted-foreground"
                            />
                            <span class="mt-2 text-sm text-muted-foreground">
                                Cliquez pour ajouter une image
                            </span>
                            <input
                                ref="fileInput"
                                type="file"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                class="hidden"
                                @change="handleImageChange"
                            />
                        </label>
                    </div>
                    <InputError :message="form.errors.featured_image" />
                </div>

                <!-- Status & Published At -->
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="status">Statut</Label>
                        <Select
                            v-model="form.status"
                            :disabled="form.processing"
                        >
                            <SelectTrigger>
                                <SelectValue
                                    placeholder="Sélectionner un statut"
                                />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="status in statuses"
                                    :key="status.value"
                                    :value="status.value"
                                >
                                    {{ status.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />
                    </div>

                    <div v-if="form.status === 'scheduled'" class="space-y-2">
                        <Label for="published_at">Date de publication</Label>
                        <Input
                            id="published_at"
                            type="datetime-local"
                            v-model="form.published_at"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.published_at" />
                    </div>
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4 pt-4">
                    <Button variant="outline" as-child>
                        <Link :href="index().url">Annuler</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing ? 'Création...' : "Créer l'article"
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
