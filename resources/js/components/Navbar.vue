<template>
    <va-navbar color="primary" class="mb-3">
        <template #left>
            <va-navbar-item class="logo">Student Grades Sample</va-navbar-item>
        </template>
        <template #right>
            <va-navbar-item>Dashboard</va-navbar-item>
            <va-navbar-item>Students</va-navbar-item>
            <va-navbar-item>Subjects</va-navbar-item>
            <va-navbar-item>
                <va-icon name="search" color="textInverted" />
            </va-navbar-item>
            <va-navbar-item>
                <va-icon name="account_circle" color="textInverted" />
            </va-navbar-item>
        </template>
    </va-navbar>
</template>

<style scoped>
.logo {
    font-weight: 600;
    font-size: 1.5rem;
}
</style>

<script setup>
    import { computed } from 'vue'
    import { useColors } from 'vuestic-ui'

    const { currentPresetName } = useColors()

    const darkNavbarColors = computed(() => {
        if (currentPresetName.value === 'light') {
            return {
            color: '#111111',
            textColor: '#BAFFC5',
            }
        } else {
            return {
            color: '#FBCAF6',
            textColor: '#481269',
            }
        }
    })
</script>
