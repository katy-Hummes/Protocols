<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const form = useForm('post', route('register'), {
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
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Register" />

    <AuthenticationCard>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nome" class="text-white" />
                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autofocus
                    autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" class="text-white" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
                    autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="type" value="Perfil" class="text-white" />
                <select v-model="form.type" class="mt-1 block w-full bg-slate-50 rounded-md shadow-sm">
                    <option value="">selecione</option>
                    <option value="T" v-if="$page.props.auth.user.type === 'T'">Administrador da TI</option>
                    <option value="S" v-if="$page.props.auth.user.type === 'T'">Administrador do sistema</option>
                    <option value="A" v-if="$page.props.auth.user.type === 'T' || $page.props.auth.user.type === 'S'">
                        Atendente</option>
                </select>
                <InputError class="mt-2" :message="form.errors.type" />
            </div>

            <div class="mt-4">
                <InputLabel for="cpf" value="CPF" class="text-white" />
                <TextInput id="cpf" v-model="form.cpf" type="text" class="mt-1 block w-full" required autofocus
                    v-mask="'###.###.###-##'" autocomplete="cpf" @change="form.validate('cpf')" />
                <span v-if="form.invalid('description')" class="text-base text-red-500">
                    {{ form.errors.description }}
                </span>
                <InputError class="mt-2" :message="form.errors.cpf" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Senha" class="text-white" />
                <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
                    autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirme a Senha" class="text-white" />
                <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
                    class="mt-1 block w-full" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-4">
                <InputLabel for="active" value="active" class="text-white" />
                <select id="active" v-model="form.active" class="mt-1 block w-full bg-slate-50 rounded-md shadow-sm"
                    disabled autofocus>
                    <option value="S">Ativo</option>
                    <option value="N">Desativado</option>
                </select>
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2 text-white">
                            Estou de acordo com <a target="_blank" :href="route('terms.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Terms
                                of Service</a> and <a target="_blank" :href="route('policy.show')"
                                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Privacy
                                Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-between mt-4 ">
                <Link :href="route('login')"
                    class="text-white underline text-sm hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Já registrado?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Registrar
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
