<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const toast = useToast();
const props = defineProps({
    user: Object,
});

const form = useForm('post', route('user.update', props.user.id), {
    name: props.user.name,
    email: props.user.email,
    type: props.user.type,
    cpf: props.user.cpf,
    active: props.user.active,
});

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => {
        toast.open({
            message: 'Usuário atualizado com sucesso!',
            type: 'success',
            position: 'top-right',
        });
    },
    onError: () => {
        toast.open({
            message: 'Erro ao atualizar usuário!',
            type: 'error',
            position: 'top-right',
        });
    },
});

const translateActive = (active) => {
    switch (active) {
        case 'S':
            return 'Ativo';
        case 'N':
            return 'Desativado';
    }
};
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Visualizar Usuário
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <v-container>
                        <form @submit.prevent="submit">
                            <v-container>
                                <v-text-field label="Nome:" v-model="form.name" variant="outlined"
                                    @change="form.validate('name')" :disabled="props.user.type === 'T'">
                                </v-text-field>
                                <span v-if="form.invalid('name')" class="text-base text-red-500">
                                    {{ form.errors.name }}
                                </span>
                            </v-container>

                            <v-container>
                                <v-text-field label="Email:" v-model="form.email" variant="outlined" disabled
                                    @change="form.validate('email')"></v-text-field>
                                <span v-if="form.invalid('email')" class="text-base text-red-500">
                                    {{ form.errors.email }}
                                </span>
                            </v-container>

                            <v-container>
                                <v-text-field label="CPF:" v-model="form.cpf" variant="outlined" disabled
                                    v-mask="'###.###.###-##'" @change="form.validate('cpf')"></v-text-field>
                                <span v-if="form.invalid('cpf')" class="text-base text-red-500">
                                    {{ form.errors.cpf }}
                                </span>
                            </v-container>

                            <v-container class="mt-4">
                                <InputLabel for="type" value="Perfil" class="text-gray-900" />
                                <select v-model="form.type" @change="form.validate('type')"
                                    class="mt-1 block w-full bg-slate-50 rounded-md shadow-sm"
                                    :disabled="props.user.type === 'T'">
                                    <option value="">selecione</option>
                                    <option value="T" v-if="$page.props.auth.user.type === 'T'">Administrador da TI
                                    </option>
                                    <option value="S" v-if="$page.props.auth.user.type === 'T'">Administrador do sistema
                                    </option>
                                    <option value="A"
                                        v-if="$page.props.auth.user.type === 'T' || $page.props.auth.user.type === 'S'">
                                        Atendente</option>
                                </select>
                                <span v-if="form.invalid('type')" class="text-base text-red-500">
                                    {{ form.errors.type }}
                                </span>
                            </v-container>

                            <v-container class="mt-4">
                                <InputLabel value="Situação" class="text-gray-900" />
                                <select id="active" v-model="form.active"
                                    class="mt-1 block w-full bg-slate-50 rounded-md shadow-sm" :disabled="props.user.type === 'T'" autofocus>
                                    <option selected :value="form.active">{{ translateActive(form.active) }}</option>
                                    <option value="S">Ativo</option>
                                    <option value="N">Desativado</option>
                                </select>
                            </v-container>

                            <div class="flex items-center justify-between mt-4 ">
                                <Link :href="route('users.index')"
                                    class="text-base  font-semibold border-2 border-gray-600 rounded-3xl mx-4 px-4 py-1 hover:bg-purple-800 hover:text-white">
                                Voltar</Link>
                                <v-btn type="submit" class="ms-4">
                                    Salvar
                                </v-btn>
                            </div>

                        </form>
                    </v-container>
                </div>
            </div>
        </div>
    </AppLayout>
</template>