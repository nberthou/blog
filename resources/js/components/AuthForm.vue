<script setup lang="ts">

import { Form } from '@inertiajs/vue3';
import { store } from "@/routes/login";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";

type Props = {
    type: "LOGIN" | "REGISTER"
}

const props = defineProps<Props>();

const formFields = {
    LOGIN: [{name: 'email', label: 'Adresse mail', placeholder: 'email@exemple.com', type: 'text'}, {name: 'password', label: 'Mot de passe', placeholder: 'Mot de passe', type: 'text'}, {name: 'remember', label: 'Se souvenir de moi', placeholder: null, type: 'checkbox'}],
    REGISTER: [{name: 'name', label: 'Nom', placeholder: 'Nom complet', type: 'text'}, {name: 'email', label: 'Adresse mail', placeholder: 'email@exemple.com', type: 'text'}, {name: 'password', label: 'Mot de passe', placeholder: 'Mot de passe', type: 'text'}, {name: 'password_confirmation', label: 'Confirmer le mot de passe', placeholder: 'Confirmer le mot de passe', type: 'text'}],
};

</script>

<template>
    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
        >
        <div class="grid gap-6">
                <div class="grid gap-2" v-for="field in formFields[type]" :key="field.name">
                    <Label for="field.name">{{field.label}}</Label>
                    <Input
                        :key="field.name"
                        :type="field.type"
                        required
                        autofocus
                        :tabindex="1"
                        :name="field.name"
                        :placeholder="field.placeholder"
                    />
                </div>
        </div>
    </Form>
</template>

<style scoped>

</style>
