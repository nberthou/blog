<script setup lang="ts">
import { index, store } from '@/routes/categories';
import { Head, Link, useForm } from '@inertiajs/vue3';

import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ArrowLeft } from 'lucide-vue-next';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Catégories',
        href: index().url,
    },
    {
        title: 'Nouvelle catégorie',
        href: '#',
    },
];

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Nouvelle catégorie" />

        <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back Link -->
            <div class="mb-6">
                <Button variant="ghost" size="sm" as-child>
                    <Link :href="index().url">
                        <ArrowLeft class="mr-2 h-4 w-4" />
                        Retour aux catégories
                    </Link>
                </Button>
            </div>

            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold tracking-tight">
                    Nouvelle catégorie
                </h1>
                <p class="mt-1 text-muted-foreground">
                    Créez une nouvelle catégorie pour organiser vos articles
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Name -->
                <div class="space-y-2">
                    <Label for="name">Nom *</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        placeholder="Ex: Technologie, Voyage, Cuisine..."
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <Label for="description">Description</Label>
                    <Textarea
                        id="description"
                        v-model="form.description"
                        placeholder="Une courte description de la catégorie (optionnel)"
                        rows="3"
                        :disabled="form.processing"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <!-- Submit -->
                <div class="flex justify-end gap-4 pt-4">
                    <Button variant="outline" as-child>
                        <Link :href="index().url">Annuler</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{
                            form.processing
                                ? 'Création...'
                                : 'Créer la catégorie'
                        }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
