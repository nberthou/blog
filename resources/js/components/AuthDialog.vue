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

const emit = defineEmits<{
    success: [];
}>();

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
    emit('success');
}

</script>

<template>
    <DropdownMenuItem :as-child>
        <Dialog v-model:open="open">
            <DialogTrigger class="relative flex w-full cursor-default select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground" @click.stop>
                {{ props.type === "LOGIN" ? "Se connecter": "S'inscrire" }}
            </DialogTrigger>
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
