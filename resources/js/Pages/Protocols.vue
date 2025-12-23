<script setup>
import { ref, computed, defineProps, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import jsPDF from 'jspdf';

const toast = useToast();

const props = defineProps({
    peoples: Array,
    protocols: Array,
    departments: Array,
    reports: Array,
});

const dateReset = new Date(props.protocols.date);
const form = useForm('post', route('protocol.store'), {
    description: '',
    term: '',
    date: isNaN(dateReset.getTime()) ? null : dateReset,
    people_id: null,
    department_id: null,
    files: [],
});

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => {
        form.reset();
        toast.success("Protocolo criado com Sucesso!", {
            position: 'top-right',
        });
    },
    onError: (errors) => {
        toast.open({
            message: errors.files ? errors.files.join(', ') : 'Erro ao criar protocolo!',
            type: 'error',
            position: 'top-right',
        });
    },
});

const validateDate = () => {
    if (!form.date || isNaN(new Date(form.date).getTime())) {
        form.errors.date = 'A data é obrigatória e deve ser válida.';
        return false;
    } else {
        form.errors.date = null;
        return true;
    }
}

const updateDateReset = (newValue) => {
    if (!newValue) {
        form.date = null;
    } else {
        const newDate = new Date(newValue);
        if (!isNaN(newDate.getTime())) {
            form.date = newDate;
        }
    }
    validateDate();
}

function validateFiles() {
    const validTypes = ['application/pdf', 'image/png', 'image/jpeg'];
    const maxFiles = 5;
    const maxSize = 3 * 1024 * 1024; // 3MB em bytes

    if (form.files.length > maxFiles) {
        console.log('maxFiles')
        toast.open({
            message: `Anexar no máximo ${maxFiles} arquivos.`,
            type: 'error',
            position: 'top-right',
            duration: 10000
        });
        form.files = []
        form.errors.files = [`Número máximo de ${maxFiles}.`];
        return false;
    }

    for (let file of form.files) {
        if (!validTypes.includes(file.type)) {
            console.log('maxFiles')
            toast.open({
                message: 'Apenas PDF, PNG, JPG ou JPEG são permitidos.',
                type: 'error',
                position: 'top-right',
                duration: 10000
            });
            form.files = []
            form.errors.files = ['Tipo de arquivo inválido.'];
            return false;
        }

        if (file.size > maxSize) {
            toast.open({
                message: 'Cada arquivo deve ter no máximo 3MB.',
                type: 'error',
                position: 'top-right',
                duration: 10000
            });
            form.files = []
            form.errors.files = ['Um ou mais arquivos, excedeu o limite de 3MB.'];
            return false;
        }
    }

    form.errors.files = [];

    return true;
}

// configuração de datas
const isMenuOpen = ref(false);
const formattedDate = computed(() => {
    if (!form.date) return '';
    const dateObj = new Date(form.date);
    return dateObj.toLocaleDateString('pt-BR');
});

// Filtros e paginação
const search = ref('');
const page = ref(1);
const itemsPerPage = 10;

const filteredProtocols = computed(() => {
    const searchTerm = search.value.toLowerCase().trim();
    page.value = 1
    return props.protocols.filter(protocol => {
        return (
            protocol.people.name.toLowerCase().includes(searchTerm) ||
            formatDate(protocol.date).toLowerCase().includes(searchTerm) ||
            protocol.id.toString().toLowerCase().includes(searchTerm) ||
            protocol.department.name.toLowerCase().includes(searchTerm) ||
            getStatusText(protocol.latest_report).toLowerCase().includes(searchTerm) ||
            protocol.description.toLowerCase().includes(searchTerm)
        );
    });
});

const displayedProtocols = computed(() => {
    const start = (page.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredProtocols.value.slice(start, end);
});

const pageCount = computed(() => {
    return Math.ceil(filteredProtocols.value.length / itemsPerPage);
});

const updatePage = (newPage) => {
    page.value = newPage;
};


// Modal deletar protocolo
const showDelete = ref(false);
const formDelete = ref();

const openDeleteModal = (id) => {
    console.log(id);
    showDelete.value = true;
    formDelete.value = useForm('delete', `/deletar-protocolo/${id}`, {
        id: id
    });
}

const closeDeleteModal = () => {
    showDelete.value = false;
}

const deleteProtocols = () => {
    formDelete.value.submit({
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteModal();
            toast.success("Protocolo deletado com Sucesso!", {
                position: 'top-right',
            });
        }
    });
}

const getStatusColor = (report) => {
    if (!report) return 'gray';
    switch (report.status) {
        case 'A': return 'red';
        case 'E': return 'yellow';
        case 'S': return 'green';
        default: return 'gray';
    }
};

const getStatusText = (report) => {
    if (!report) return 'Não Iniciado';
    switch (report.status) {
        case 'A': return 'Aberto';
        case 'E': return 'Em Atendimento';
        case 'S': return 'Solucionado';
        default: return 'Desconhecido';
    }
};

// Calcular data final
const calculateFinalDate = (startDate, term) => {
    const startDateObj = new Date(startDate);
    const finalDateObj = new Date(startDateObj.getTime() + term * 24 * 60 * 60 * 1000);
    // Convertendo para uma string legível
    return finalDateObj.toLocaleDateString();
};

// Função para formatar a data da tabela
const formatDate = (dateString) => {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const downloadPDF = () => {
    const doc = new jsPDF();
    const protocolsData = props.protocols;
    let y = 10;

    protocolsData.forEach((protocol, index) => {
        const lines = doc.splitTextToSize(`Descrição do Protocolo: ${protocol.description}`, 180);
        const lineHeight = 5;
        const descriptionHeight = lines.length * lineHeight;

        if (y + descriptionHeight > doc.internal.pageSize.height - 20) {
            doc.addPage();
            y = 10; // Reinicie a posição Y na nova página
        }

        // Adicione um cabeçalho na primeira linha de cada página
        if (y === 10 || index === 0) {
            doc.setFontSize(18);
            doc.setTextColor(0, 0, 255); // Cor azul
            doc.text('Protocolos', 10, y);
            y += 10;
        }

        // Adicione linhas de tabela com cores alternadas
        if (index % 2 === 0) {
            doc.setFillColor(230, 230, 230); // Cor de fundo para linhas ímpares
            doc.rect(10, y - 5, 190, 10, 'F'); // Adiciona retângulo de fundo
        }
        doc.setFontSize(12);
        doc.setTextColor(0);
        doc.text(`Nome do Contribuinte: ${protocol.people.name}`, 15, y);
        doc.text(`Data: ${formatDate(protocol.date)}`, 15, y + 5);
        lines.forEach((line, i) => {
            doc.text(line, 15, y + 10 + (i * lineHeight));
        });
        y += 20 + (lines.length - 1) * lineHeight; // Altura da linha + altura extra para cada linha adicional
    });

    doc.save('Protocolos.pdf');
}

</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Protocolos
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

                        <v-col cols="auto" class="flex justify-between mb-5">
                            <v-dialog transition="dialog-top-transition" max-width="600">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <v-btn v-bind="activatorProps">Criar Protocolo</v-btn>
                                </template>
                                <template v-slot:default="{ isActive }">
                                    <form @submit.prevent="submit">
                                        <v-card title="Criar Protocolo">

                                            <v-container>
                                                <v-select label="Departamento" :items="departments" item-title="name"
                                                    item-value="id" variant="outlined" v-model="form.department_id"
                                                    @change="form.validate('department_id')"></v-select>
                                                <span v-if="form.invalid('department_id')"
                                                    class="text-base text-red-500">
                                                    {{ form.errors.department_id }}
                                                </span>
                                            </v-container>

                                            <v-container>
                                                <v-select label="Contribuinte" :items="peoples" item-title="name"
                                                    item-value="id" variant="outlined" v-model="form.people_id"
                                                    @change="form.validate('people_id')"></v-select>
                                                <span v-if="form.invalid('people_id')" class="text-base text-red-500">
                                                    {{ form.errors.people_id }}
                                                </span>
                                            </v-container>

                                            <v-container>
                                                <v-menu v-model="isMenuOpen" :close-on-content-click="false">
                                                    <template v-slot:activator="{ props }">
                                                        <v-text-field label="Selecione a data:*"
                                                            :model-value="formattedDate" v-bind="props"
                                                            variant="outlined" @update:modelValue="updateDateReset" ></v-text-field>
                                                    </template>
                                                    <v-date-picker v-model="form.date"
                                                        :rules="[() => dateValidation(form.date, 30, 0)]"
                                                        @change="form.validate('date')"></v-date-picker>
                                                </v-menu>
                                                <span class="text-base text-red-500">
                                                    {{ form.errors.date }}
                                                </span>
                                            </v-container>

                                            <v-container>
                                                <v-text-field v-model="form.term" label="Prazo em dias" type="number"
                                                    variant="outlined" min="5" max="30"
                                                    @change="form.validate('term')"></v-text-field>
                                                <span v-if="form.invalid('term')" class="text-base text-red-500">
                                                    {{ form.errors.term }}</span>
                                            </v-container>

                                            <v-container>
                                                <v-textarea v-model="form.description" label="Descrição"
                                                    variant="outlined"
                                                    @change="form.validate('description')"></v-textarea>
                                                <span v-if="form.invalid('description')" class="text-base text-red-500">
                                                    {{ form.errors.description }}
                                                </span>
                                            </v-container>
                                            <!-- {{ form.files }} -->
                                            <v-container>
                                                <v-file-input label="Anexar Documentos" multiple variant="outlined"
                                                    @change="validateFiles()" v-model="form.files"></v-file-input>
                                                <span v-if="form.invalid('files')" class="text-base text-red-500">
                                                    {{ form.errors }}
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
                            <v-btn @click="downloadPDF">Baixar PDF</v-btn>
                        </v-col>

                        <v-card title="Protocolos" flat>
                            <template v-slot:text>
                                <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify"
                                    variant="outlined" hide-details single-line></v-text-field>
                            </template>
                        </v-card>
                        <v-table>
                            <thead>
                                <tr>
                                    <th class="text-left">Número</th>
                                    <th class="text-left">Contribuinte</th>
                                    <th class="text-left">Data de Abertura</th>
                                    <th class="text-left">Prazo</th>
                                    <th class="text-left">Data Final</th>
                                    <th class="text-left">Departamento</th>
                                    <th class="text-left">Situação</th>
                                    <th class="text-left">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="protocol in displayedProtocols" :key="protocol.id">
                                    <td>{{ protocol.id }}</td>
                                    <td>{{ protocol.people.name }}</td>
                                    <td>{{ formatDate(protocol.date) }}</td>
                                    <td>{{ protocol.term }}</td>
                                    <td>{{ calculateFinalDate(protocol.date, protocol.term) }}</td>
                                    <td>{{ protocol.department.name }}</td>
                                    <td>
                                        <span :style="{ color: getStatusColor(protocol.latest_report) }">●</span>
                                        {{ getStatusText(protocol.latest_report) }}
                                    </td>
                                    <td>
                                        <div class="flex gap-4">
                                            <Link :href="route('protocol.show', protocol.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor"
                                                class="w-6 h-6 hover:scale-125 ease-in-out hover:stroke-green-500">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                            </Link>

                                            <button size="small" @click="openDeleteModal(protocol.id)">
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
        </div>
    </AppLayout>

    <!-- Exclusão de Protocolo -->
    <Modal :show="showDelete" @close="closeDeleteModal">
        <div class="p-4">
            <form @submit.prevent="deleteProtocols()">
                <h2 class="flex items-center justify-center border-b-4 text-xl p-4 m-4 font-bold">Tem certeza que deseja
                    excluir este Protocolo??</h2>
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
.v-picker{
    width: 100% !important;
}
</style>