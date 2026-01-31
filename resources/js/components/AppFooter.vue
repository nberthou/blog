<script setup lang="ts">
const currentYear = new Date().getFullYear();
</script>

<template>
    <footer class="border-t border-sidebar-border/80 bg-background">
        <div class="mx-auto max-w-7xl px-4 py-6 text-center">
            <p class="text-sm text-muted-foreground">
                &copy; {{ currentYear }} Tous droits réservés.
            </p>
        </div>
    </footer>
</template>
