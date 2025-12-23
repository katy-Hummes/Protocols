<script setup>
import { ref, computed, defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const toast = useToast();

const props = defineProps({
    peoples: Array,
});

const dateReset = new Date(props.peoples.birth);
const form = useForm('post', route('people.store'), {
    name: '',
    birth: isNaN(dateReset.getTime()) ? null : dateReset,
    cpf: '',
    sex: '',
    city: '',
    neighborhood: '',
    street: '',
    number: '',
    complement: '',
});

const submit = () => {
    form.cpf = form.cpf.replace(/\D/g, '');

    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.success("Pessoa criada com Sucesso!", {
                position: 'top-right',
            });
        },
        onError: (error) => {
            console.log(error);
            toast.error("Erro ao criar Pessoa!", {
                position: 'top-right',
            });
        }
    });
};

const validateDate = () => {
    if (!form.date || isNaN(new Date(form.birth).getTime())) {
        form.errors.birth = 'A data de Nascimento é obrigatória e deve ser válida.'
        return false;
    } else {
        form.errors.birth = null;
        return true;
    }
}

const updateDateReset = (newValue) => {
    if (!newValue) {
        form.date = null;
    } else {
        const newDate = new Date(newValue);
        if (!isNaN(newDate.getTime())) {
            form.birth = newDate;
        }
    }
    validateDate();
}

// configuração de datas
const isMenuOpen = ref(false);
const formattedDate = computed(() => {
    if (!form.birth) return '';
    const dateObj = new Date(form.birth);
    return dateObj.toLocaleDateString('pt-BR');
});

// Filtros e paginação
const search = ref('');
const page = ref(1);
const itemsPerPage = 10;

const filteredPeoples = computed(() => {
    const searchTerm = search.value.toLowerCase().trim();
    page.value = 1
    return props.peoples.filter(people => {
        return (
            people.name.toLowerCase().includes(searchTerm) ||
            people.birth.toLowerCase().includes(searchTerm) ||
            formatCPF(people.cpf).toString().toLowerCase().includes(searchTerm) ||
            people.sex.toLowerCase().includes(searchTerm)
        );
    });
});

const displayedPeoples = computed(() => {
    const start = (page.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredPeoples.value.slice(start, end);
});

const pageCount = computed(() => {
    return Math.ceil(filteredPeoples.value.length / itemsPerPage);
});

const updatePage = (newPage) => {
    page.value = newPage;
};

// Função para formatar a data da tabela
const formatDate = (dateString) => {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

// Função para formatar a CPF da tabela
const formatCPF = (cpf) => {
    if (cpf && cpf.length === 11) {
        return `${cpf.slice(0, 3)}.${cpf.slice(3, 6)}.${cpf.slice(6, 9)}-${cpf.slice(9, 11)}`;
    }
    return cpf;
};

// Modal deletar pessoas
const showDelete = ref(false);
const formDelete = ref();

const openDeleteModal = (id) => {
    console.log(id);
    showDelete.value = true;
    formDelete.value = useForm('delete', `/deletar-pessoa/${id}`, {
        id: id
    });
}

const closeDeleteModal = () => {
    showDelete.value = false;
}

const deletePeople = () => {
    formDelete.value.submit({
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
            toast.success("Pessoa excluída com Sucesso!", {
                position: 'top-right',
            });
        }
    });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pessoas
            </h2>
        </template>
        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <v-col cols="auto" class="flex justify-center mb-5">
                        <v-dialog max-width="800">
                            <template v-slot:activator="{ props: activatorProps }">
                                <v-btn v-bind="activatorProps" color="primary" text="Incluir Pessoas"
                                    variant="flat"></v-btn>
                            </template>

                            <template v-slot:default="{ isActive }">
                                <v-card title="Incluir Pessoas">
                                    <form @submit.prevent="submit">
                                        <v-card-text>
                                            <h2>Dados Pessoais:</h2>
                                            <v-container>
                                                <v-text-field label="Nome:*" v-model="form.name" variant="outlined"
                                                    @change="form.validate('name')"></v-text-field>
                                                <span v-if="form.invalid('name')" class="text-base text-red-500">
                                                    {{ form.errors.name }}
                                                </span>
                                            </v-container>
                                            <v-container>
                                                <v-menu v-model="isMenuOpen" :close-on-content-click="false">
                                                    <template v-slot:activator="{ props }">
                                                        <v-text-field label="Selecione a data:*"
                                                            :model-value="formattedDate" v-bind="props"
                                                            variant="outlined"
                                                            @update:modelValue="updateDateReset"></v-text-field>
                                                    </template>
                                                    <v-date-picker v-model="form.birth"
                                                        :rules="[() => dateValidation(form.birth, 30, 0)]"
                                                        @change="form.validate('birth')"></v-date-picker>
                                                </v-menu>
                                                <span class="text-base text-red-500">
                                                    {{ form.errors.birth }}
                                                </span>
                                            </v-container>
                                            <v-container>
                                                <v-text-field label="cpf:*" v-model="form.cpf" variant="outlined"
                                                    v-mask="'###.###.###-##'"
                                                    @change="form.validate('cpf')"></v-text-field>
                                                <span v-if="form.invalid('cpf')" class="text-base text-red-500">
                                                    {{ form.errors.cpf }}
                                                </span>
                                            </v-container>
                                            <v-container>
                                                <div class="flex">
                                                    <h2>sexo:*</h2>
                                                    <v-radio-group v-model="form.sex" inline
                                                        @change="form.validate('sex')">
                                                        <v-radio label="Masculino" value="Masculino"></v-radio>
                                                        <v-radio label="Feminino" value="Feminino"></v-radio>
                                                        <v-radio label="Outro" value="Outro"></v-radio>
                                                    </v-radio-group>
                                                </div>
                                                <span v-if="form.invalid('sex')" class="text-base text-red-500">
                                                    {{ form.errors.sex }}
                                                </span>
                                            </v-container>

                                            <h2>Endereço:</h2>
                                            <div class="grid gap-4 mb-7">
                                                <div>
                                                    <v-text-field label="Cidade" v-model="form.city"
                                                        variant="outlined"></v-text-field>
                                                </div>
                                                <div>
                                                    <v-text-field label="Bairro" v-model="form.neighborhood"
                                                        variant="outlined"></v-text-field>
                                                </div>
                                                <div>
                                                    <v-text-field label="Rua" v-model="form.street"
                                                        variant="outlined"></v-text-field>
                                                </div>
                                                <div>
                                                    <v-text-field label="Número" v-model="form.number"
                                                        variant="outlined"></v-text-field>
                                                </div>
                                                <div>
                                                    <v-textarea label="Complemento" v-model="form.complement"
                                                        variant="outlined"></v-textarea>
                                                </div>
                                            </div>
                                        </v-card-text>

                                        <v-card-actions>
                                            <v-spacer></v-spacer>

                                            <v-btn text="Cancelar" @click="isActive.value = false"></v-btn>
                                            <v-btn type="submit">Salvar</v-btn>
                                        </v-card-actions>
                                    </form>
                                </v-card>
                            </template>
                        </v-dialog>
                    </v-col>

                    <v-card title="Pessoas" flat>
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
                                <th class="text-left">Data de Nascimento</th>
                                <th class="text-left">CPF</th>
                                <th class="text-left">Sexo</th>
                                <th class="text-left">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="people in displayedPeoples" :key="people.id">
                                <td>{{ people.id }}</td>
                                <td>{{ people.name }}</td>
                                <td>{{ formatDate(people.birth) }}</td>
                                <td>{{ formatCPF(people.cpf) }}</td>
                                <td>{{ people.sex }}</td>
                                <td>
                                    <div class="flex gap-4">
                                        <Link :href="route('people.show', people.id)">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-6 h-6 hover:scale-125 ease-in-out hover:stroke-green-500">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        </Link>

                                        <button size="small" @click="openDeleteModal(people.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 hover:scale-125 ease-in-out hover:stroke-red-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                    <div class="text-center pt-2">
                        <v-pagination v-model="page" :length="pageCount" @input="updatePage"></v-pagination>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Exclusão de Pessoas -->
    <Modal :show="showDelete" @close="closeDeleteModal">
        <div class="p-4">
            <form @submit.prevent="deletePeople()">
                <h2 class="flex items-center justify-center border-b-4 text-xl p-4 m-4 font-bold">
                    Tem certeza que deseja excluir esta Pessoa?
                </h2>
                <div class="flex justify-between">
                    <v-btn type="button" @click="closeDeleteModal">
                        Cancelar
                    </v-btn>
                    <v-btn type="submit">
                        Excluir
                    </v-btn>
                </div>
            </form>
        </div>
    </Modal>
</template>
<style>
.v-picker {
    width: 100% !important;
}
</style>