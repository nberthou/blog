<script setup lang="ts">

import { Alert, AlertDescription, AlertTitle} from "@/components/ui/alert";

type Props = {
    type: "LOGIN" | "REGISTER"
}

const props = defineProps<Props>();

</script>

<template>
    <Alert>
        <AlertTitle v-if="props.type === 'LOGIN'">Vous n'êtes pas connecté(e).</AlertTitle>
        <AlertTitle v-else>Vous n'avez pas de compte.</AlertTitle>
        <AlertDescription>
            <template v-if="props.type === 'LOGIN'">
                Veuillez vous connecter pour accéder à cette fonctionnalité.
            </template>
            <template v-else>
                Veuillez créer un compte pour accéder à cette fonctionnalité.
            </template>
        </AlertDescription>
    </Alert>
</template>

<style scoped>

</style>
