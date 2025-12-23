<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed, defineProps } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const toast = useToast();
const props = defineProps({
    department: Object,
    users: Array,
    accesses: Array,
});

const avaliableUsers = computed(() => {
    return props.users.filter(user => {
        return !props.accesses.some(access => access.user_id === user.id);
    });
});


// formularios para atualizar o departamento
const form = useForm('put', route('department.update', props.department.id), {
    name: props.department.name,
});


const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => {
        toast.open({
            message: 'Departamento atualizado com sucesso!',
            type: 'success',
            position: 'top-right',
        });
    },
    onError: () => {
        toast.open({
            message: 'Erro ao atualizar Departamento!',
            type: 'error',
            position: 'top-right',
        });
    },
});

// formulario para liberar acesso
const formAccess = useForm('post', route('access', props.department.id), {
    user_id: null,
});

const submitAccess = () => formAccess.submit({
    preserveScroll: true,
    onSuccess: () => {
        toast.open({
            message: 'Acesso liberado com sucesso!',
            type: 'success',
            position: 'top-right',
        });
    },
    onError: () => {
        toast.open({
            message: 'Erro ao liberar o acesso!',
            type: 'error',
            position: 'top-right',
        });
    },
});

// Modal para confirmar a remoção de um acesso
const accessId = ref()
const showDeleteAccessConfirmModal = ref(false);
const formDeleteAccess = ref(null)

const openDeleteAccessConfirmModal = (access) => {
    console.log(access)
    accessId.value = access.id;
    formDeleteAccess.value = useForm('post', route('access.destroy', accessId.value), {
        access: access
    });
    showDeleteAccessConfirmModal.value = true;
}

const removeAccess = () => {
    formDeleteAccess.value.submit({
        preserveScroll: true,
        onSuccess: () => {
            toast.open({
                message: 'Acesso removido com sucesso!',
                type: 'success',
                position: 'top-right',
            });
            closeDeleteAccessConfirmModal();
        },
        onError: () => {
            toast.open({
                message: 'Ocorreu um erro ao remover o acesso, tente novamente!',
                type: 'error',
                position: 'top-right',
            });
            closeDeleteAccessConfirmModal();
        }
    })
}

const closeDeleteAccessConfirmModal = () => {
    showDeleteAccessConfirmModal.value = false
}

// Função para formatar a data da criação do acesso
const formatDate = (dateString) => {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};
</script>

<template>
    <Modal :show="showDeleteAccessConfirmModal" @close="closeDeleteAccessConfirmModal">
        <div class="p-4">
            <h2 class="flex items-center justify-center border-b-4 text-xl p-4 m-4 font-bold">
                Tem certeza que deseja Remover o Acesso?
            </h2>
            <div class="flex justify-between items-center">
                <v-btn type="button" @click.native="closeDeleteAccessConfirmModal">
                    Cancelar
                </v-btn>
                <form @submit.prevent="removeAccess(accessId)">
                    <v-btn type="submit">Sim, Remover Acesso {{ accessId }}</v-btn>
                </form>
            </div>
        </div>
    </Modal>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Visualizar Departamento
            </h2>
        </template>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                <v-card title="Usuários" flat>
                    <form @submit.prevent="submitAccess">
                        <v-container class="flex justify-between items-center">
                            <v-select label="Usuários" :items="avaliableUsers" item-title="name"
                                v-model="formAccess.user_id" item-value="id" variant="outlined"></v-select>
                            <v-btn class="m-4 p-3" type="submit" :disabled="!formAccess.user_id"
                                v-if="$page.props.auth.user.type === 'T' || $page.props.auth.user.type === 'S'">
                                Liberar Acesso
                            </v-btn>
                        </v-container>
                    </form>
                    <v-divider></v-divider>
                    <v-container>
                        <h1>Usuários com acesso:</h1>
                        <div v-for="access in accesses" :key="access.id" cols="12" md="6" ref="updateKey">
                            <v-card class="m-4">
                                <div class="flex justify-between m-4">
                                    <div>{{ access.id }}</div>
                                    <div>{{ access.user.name }}</div>
                                    <div>{{ formatDate(access.created_at) }}</div>
                                    <div>
                                        <v-btn color="error" @click="openDeleteAccessConfirmModal(access)">Remover
                                            Acesso</v-btn>
                                    </div>
                                </div>
                            </v-card>
                        </div>
                    </v-container>
                </v-card>
                <form @submit.prevent="submit">
                    <v-card>
                        <v-container>
                            <v-text-field label="Nome:*" v-model="form.name" variant="outlined"
                                @change="form.validate('name')"></v-text-field>
                            <span v-if="form.invalid('name')" class="text-base text-red-500">
                                {{ form.errors.name }}
                            </span>
                        </v-container>

                        <v-divider></v-divider>
                        <div class="flex justify-between items-center">
                            <Link :href="route('departments.index')"
                                class="text-base font-semibold border-2 border-gray-600 rounded-3xl mx-4 px-4 py-1 hover:bg-purple-800 hover:text-white">
                            Voltar</Link>

                            <v-btn class="m-4" type="submit" color="primary">Salvar</v-btn>
                        </div>
                    </v-card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>