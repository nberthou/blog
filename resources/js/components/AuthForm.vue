<script setup lang="ts">

import { Form } from '@inertiajs/vue3';
import { store as loginStore } from "@/routes/login";
import { store as registerStore } from "@/routes/register";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {Button} from "@/components/ui/button";
import {Spinner} from "@/components/ui/spinner";
import InputError from "@/components/InputError.vue";

type Props = {
    type: "LOGIN" | "REGISTER"
}

const props = defineProps<Props>();

const formFields = {
    LOGIN: [{name: 'email', label: 'Adresse mail', placeholder: 'email@exemple.com', type: 'text'}, {name: 'password', label: 'Mot de passe', placeholder: 'Mot de passe', type: 'password'}, {name: 'remember', label: 'Se souvenir de moi', placeholder: null, type: 'checkbox'}],
    REGISTER: [{name: 'name', label: 'Nom', placeholder: 'Nom complet', type: 'text'}, {name: 'email', label: 'Adresse mail', placeholder: 'email@exemple.com', type: 'text'}, {name: 'password', label: 'Mot de passe', placeholder: 'Mot de passe', type: 'password'}, {name: 'password_confirmation', label: 'Confirmer le mot de passe', placeholder: 'Confirmer le mot de passe', type: 'password'}],
};

const store = props.type === "LOGIN" ? loginStore : registerStore;

const buttonLabels = {
    LOGIN: "Se connecter",
    REGISTER: "S'inscrire"
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
                    <InputError :message="errors[field.name]" />
                </div>
        </div>

        <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="processing">
            <Spinner v-if="processing" />
            {{ buttonLabels[type] }}
        </Button>
    </Form>
</template>

<style scoped>

</style>
