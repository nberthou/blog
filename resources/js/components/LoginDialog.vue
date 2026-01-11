<script setup lang="ts">

import { Alert, AlertDescription, AlertTitle} from "@/components/ui/alert";
import {
    Dialog,
    DialogContent,
    DialogTitle,
    DialogClose,
    DialogTrigger,
    DialogOverlay,
    DialogDescription
} from "@/components/ui/dialog";
import {DropdownMenuGroup, DropdownMenuItem} from "@/components/ui/dropdown-menu";
import { DialogPortal } from "reka-ui";


type Props = {
    type: "LOGIN" | "REGISTER"
}

const props = defineProps<Props>();

</script>

<template>
    <DropdownMenuItem :as-child>
        <Dialog>
            <DialogTrigger @click.stop>{{ props.type === "LOGIN" ? "Se connecter": "S'inscrire" }}</DialogTrigger>
            <DialogPortal :to="'body'">
                <DialogOverlay />
                <DialogContent>
                    <DialogTitle>{{props.type === "LOGIN" ? "Se connecter": "S'inscrire"}}</DialogTitle>
                    <DialogDescription>
                        <Alert variant="info">
                            <AlertTitle>Fonctionnalité en cours de développement</AlertTitle>
                            <AlertDescription>
                                Cette fonctionnalité sera disponible prochainement. Restez à l'écoute !
                            </AlertDescription>
                        </Alert>
                    </DialogDescription>
                    <DialogClose />
                </DialogContent>
            </DialogPortal>
        </Dialog>
    </DropdownMenuItem>
</template>

<style scoped>

</style>
