<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import AppLayout from '@/Layouts/AppLayout.vue';

const toast = useToast();

const form = useForm('post', route('user.register'), {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    type: '',
    cpf: '',
    active: 'S',
});

const submit = () => {
    form.cpf = form.cpf.replace(/\D/g, '');
    
    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.success("Usuário criado com Sucesso!", {
                position: 'top-right',
            });
        },
        onError: () => {
            toast.error("Erro ao criar Usuário!", {
                position: 'top-right',
            });
        }
    });
};

</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Criar Usuário
            </h2>
        </template>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <v-card>
                <form @submit.prevent="submit">
                    <v-container>
                        <v-text-field label="Nome:*" v-model="form.name" variant="outlined"
                            @change="form.validate('name')"></v-text-field>
                        <span v-if="form.invalid('name')" class="text-base text-red-500">
                            {{ form.errors.name }}
                        </span>
                    </v-container>

                    <v-container>
                        <v-text-field label="Email:*" v-model="form.email" variant="outlined"
                            @change="form.validate('email')"></v-text-field>
                        <span v-if="form.invalid('email')" class="text-base text-red-500">
                            {{ form.errors.email }}
                        </span>
                    </v-container>

                    <v-container>
                        <v-text-field label="CPF:" v-model="form.cpf" variant="outlined" v-mask="'###.###.###-##'"
                            @change="form.validate('cpf')"></v-text-field>
                        <span v-if="form.invalid('cpf')" class="text-base text-red-500">
                            {{ form.errors.cpf }}
                        </span>
                    </v-container>

                    <v-container>
                        <v-text-field label="Senha:*" v-model="form.password" variant="outlined"
                            @change="form.validate('password')" type="password"
                            autocomplete="new-password"></v-text-field>
                        <span v-if="form.invalid('password')" class="text-base text-red-500">
                            {{ form.errors.password }}
                        </span>
                    </v-container>

                    <v-container>
                        <v-text-field label="Confirme a Senha:*" v-model="form.password_confirmation" variant="outlined"
                            @change="form.validate('password_confirmation')" type="password"
                            autocomplete="new-password"></v-text-field>
                        <span v-if="form.invalid('password_confirmation')" class="text-base text-red-500">
                            {{ form.errors.password_confirmation }}
                        </span>
                    </v-container>

                    <v-container>
                        <!-- {{ $page.props.auth.user. }} -->
                        <InputLabel for="type" value="Perfil:*" class="text-white" />
                        <select v-model="form.type" class="border mt-1 block w-full bg-slate-50 rounded-md shadow-sm">
                            <option value="">Perfil:*</option>
                            <option value="T" v-if="$page.props.auth.user.type === 'T'">Administrador da TI</option>
                            <option value="S" v-if="$page.props.auth.user.type === 'T'">Administrador do sistema
                            </option>
                            <option value="A"
                                v-if="$page.props.auth.user.type === 'T' || $page.props.auth.user.type === 'S'">
                                Atendente</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.type" />
                    </v-container>

                    <v-container class="flex items-center justify-between mt-4 ">
                        <Link :href="route('users.index')"
                            class="text-base  font-semibold border-2 border-gray-600 rounded-3xl mx-4 px-4 py-1 hover:bg-purple-800 hover:text-white">
                        Voltar</Link>
                        <v-btn type="submit" class="ms-4">
                            Registrar
                        </v-btn>
                    </v-container>
                </form>
            </v-card>
        </div>
    </AppLayout>
</template>
