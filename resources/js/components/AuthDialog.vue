<script setup lang="ts">

import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogClose,
    DialogTrigger,
    DialogOverlay,
    DialogDescription
} from "@/components/ui/dialog";
import {DropdownMenuItem} from "@/components/ui/dropdown-menu";
import { DialogPortal } from "reka-ui";
import AuthForm from "@/components/AuthForm.vue";
import { ref } from "vue";


type Props = {
    type: "LOGIN" | "REGISTER"
}

const props = defineProps<Props>();

const open = ref(false);

const dialogInfos = {
    LOGIN: {
        title: "Se connecter",
        description: "Connectez-vous à votre compte en entrant votre email et mot de passe ci-dessous."
    },
    REGISTER: {
        title: "S'inscrire",
        description: "Créez un compte en entrant vos informations ci-dessous."
    }
};

function closeDialog() {
    open.value = false;
}

</script>

<template>
    <DropdownMenuItem :as-child>
        <Dialog v-model:open="open">
            <DialogTrigger @click.stop>{{ props.type === "LOGIN" ? "Se connecter": "S'inscrire" }}</DialogTrigger>
            <DialogPortal :to="'body'">
                <DialogOverlay />
                <DialogContent>
                    <DialogTitle>{{dialogInfos[type].title}}</DialogTitle>
                    <DialogDescription>
                        <p class="text-sm text-muted-foreground">
                            {{ dialogInfos[type].description }}
                        </p>
                    </DialogDescription>
                    <AuthForm :type="type" @success="closeDialog" />
                    <DialogClose />
                </DialogContent>
            </DialogPortal>
        </Dialog>
    </DropdownMenuItem>
</template>

<style scoped>

</style>
