<script setup>
import { ref, computed, defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    departments: Array,
});

// Filtros e paginação
const search = ref('');
const page = ref(1);
const itemsPerPage = 10;

const filteredDepartments = computed(() => {
    const searchTerm = search.value.toLowerCase().trim();
    return props.departments.filter(department => {
        return (
            department.name.toLowerCase().includes(searchTerm)
        );
    });
});

const displayedDepartaments = computed(() => {
    const start = (page.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredDepartments.value.slice(start, end);
});

const pageCount = computed(() => {
    return Math.ceil(filteredDepartments.value.length / itemsPerPage);
});

const updatePage = (newPage) => {
    page.value = newPage;
};

// inclusão de Departamento
const toast = useToast();
const form = useForm('post', route('department.store'), {
    name: '',
});

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => {
        form.reset();
        toast.success("Departamento criado com Sucesso!", {
            position: 'top-right',
        });
    },
    onError: () => {
        toast.error("Erro ao atualizar Departamento!", {
            position: 'top-right',
        });
    }
});


</script>
<template>
    <AppLayout>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <v-card>
                <div class="bg-purple-100 flex justify-between items-center p-4">
                    <v-tab value="one">Departamentos</v-tab>
                    <v-dialog transition="dialog-top-transition" max-width="500">
                        <template v-slot:activator="{ props: activatorProps }">
                            <v-btn v-bind="activatorProps"
                                v-if="$page.props.auth.user.type === 'T' || $page.props.auth.user.type === 'S'">Criar
                                Departamento</v-btn>
                        </template>

                        <template v-slot:default="{ isActive }">
                            <form @submit.prevent="submit">
                                <v-card title="Criar Departamento">
                                    <v-container>
                                        <v-text-field label="Nome:*" v-model="form.name" variant="outlined"
                                            @change="form.validate('name')"></v-text-field>
                                        <span v-if="form.invalid('name')" class="text-base text-red-500">
                                            {{ form.errors.name }}
                                        </span>
                                    </v-container>

                                    <v-divider></v-divider>
                                    <div class="flex">
                                        <v-card-actions>
                                            <v-btn text="Cancelar" @click="isActive.value = false"></v-btn>
                                        </v-card-actions>
                                        <v-row>
                                            <v-col cols="12" class="text-right">
                                                <v-btn type="submit" color="primary">Salvar</v-btn>
                                            </v-col>
                                        </v-row>
                                    </div>
                                </v-card>
                            </form>
                        </template>
                    </v-dialog>
                </div>
                <v-card-text>
                    <v-card title="Departamentos" flat>
                        <v-card flat>
                            <template v-slot:text>
                                <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                                    variant="outlined" hide-details single-line></v-text-field>
                            </template>
                        </v-card>

                        <v-table>
                            <thead>
                                <tr>
                                    <th class="text-left">Id</th>
                                    <th class="text-left">Nome</th>
                                    <th class="text-left">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="department in displayedDepartaments" :key="department.id">
                                    <td>{{ department.id }}</td>
                                    <td>{{ department.name }}</td>
                                    <td>
                                        <div class="flex gap-4">
                                            <Link :href="route('department.show', department.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 hover:scale-125 ease-in-out hover:stroke-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            </Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </v-table>
                        <div class="text-center pt-2">
                            <v-pagination v-model="page" :length="pageCount" @input="updatePage"></v-pagination>
                        </div>
                    </v-card>
                </v-card-text>
            </v-card>
        </div>
    </AppLayout>
</template>