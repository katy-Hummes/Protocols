<script setup>
import { ref, computed, defineProps, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useForm } from 'laravel-precognition-vue-inertia';
import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';
import AppLayout from '@/Layouts/AppLayout.vue';
import jsPDF from 'jspdf';
import 'jspdf-autotable';
import axios from 'axios';

const toast = useToast();

const tab = ref('one');

const props = defineProps({
    protocol: Object,
    peoples: Array,
    departments: Array,
    reports: Array,
});

// Editar Protocolos
const form = useForm('put', route('protocol.update', props.protocol.id), {
    id: props.protocol.id,
    date: new Date(props.protocol.date),
    people_id: props.protocol.people_id,
    department_id: props.protocol.department_id,
    term: props.protocol.term,
    description: props.protocol.description,
});

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => {
        toast.open({
            message: 'Protocolo atualizado com sucesso!',
            type: 'success',
            position: 'top-right',
        });
    },
    onError: () => {
        toast.open({
            message: 'Erro ao atualizar protocolo!',
            type: 'error',
            position: 'top-right',
        });
    },
});

// configuração de data do input
const isMenuOpen = ref(false);
const selectedDate = ref()
const formattedDate = computed(() => {
    if (!form.date) return '';
    const dateObj = new Date(form.date);
    return dateObj.toLocaleDateString('pt-BR');
});

watch(selectedDate, (newValue, oldValue) => {
    isMenuOpen.value = false
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

const updateDate = (newValue) => {
    if (!newValue) {
        form.date = null;
    } else {
        const newDate = new Date(newValue);
        if (!isNaN(newDate.getTime())) {
            form.date = newDate;
        }
    }
    validateDate();
};

// configurações das files 
const isImage = (filename) => {
    return /\.(jpg|jpeg|png)$/i.test(filename);
};

const deleteFile = async (attach) => {
    await axios.delete(`/api/docattachs/${attach.id}`);
    props.protocol.docattachs = props.protocol.docattachs.filter(a => a.id !== attach.id);
};

const downloadFile = async (filename) => {
    try {
        const response = await axios({
            url: `/storage/${filename}`,
            method: 'GET',
            responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();

        link.parentNode.removeChild(link);
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Erro ao baixar o arquivo:', error);
    }
};

// script Acompanhamentos:
const formReport = useForm('post', route('store.Report'), {
    protocol_id: props.protocol.id,
    description: '',
    status: '',
});

const reportSubmit = () => formReport.submit({
    preserveScroll: true,
    onSuccess: () => {
        formReport.reset();
        toast.success("Acompanhamento criado com Sucesso!", {
            position: 'top-right',
        });
    },
    onError: () => {
        toast.error("Erro ao criar Acompanhamento!", {
            position: 'top-right',
        });
    }
});

const reversedReports = computed(() => {
    return [...props.reports].reverse();
});

const formatDate = (dateString) => {
    const options = {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit', hour12: false 
    };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const translateStatus = (status) => {
    switch (status) {
        case 'A':
            return 'Aberto';
        case 'E':
            return 'Em atendimento';
        case 'S':
            return 'Solucionado';
    }
};

// Função para gerar o PDF
const generatePDF = () => {
  const doc = new jsPDF();

  // Título
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(16);
  doc.text('Relatório Detalhado do Protocolo', 10, 10);

  // Dados principais do protocolo
  const protocolData = [
    ["Número: " + props.protocol.id, "Data de abertura: " + formatDate(form.date)],
    ["Contribuinte: " + (props.peoples.find(p => p.id === form.people_id)?.name || ""), "Data de limite: " + formatDate(form.deadline)],
    ["Departamento: " + (props.departments.find(d => d.id === form.department_id)?.name || ""), "Situação: " + translateStatus(form.status)]
  ];
  const splitDescription = doc.splitTextToSize(form.description, 180);
  protocolData.push(["Descrição: ", splitDescription.join(' ')]);
  
  doc.autoTable({
    head: [],
    body: protocolData,
    startY: 20,
    theme: 'plain',
    styles: { fontSize: 10 }
  });

  let currentY = doc.previousAutoTable.finalY + 10;

  // Título Acompanhamentos
  doc.setFontSize(14);
  doc.text('Acompanhamentos:', 10, currentY);
  currentY += 10;

  // Tabela de Acompanhamentos
  const tableColumn = ["Data", "Descrição", "Situação"];
  const tableRows = [];

  props.reports.forEach((report) => {
    const reportData = [
      formatDate(report.created_at),
      report.description.length > 2000 ? report.description.slice(0, 2000) + "..." : report.description,
      translateStatus(report.status)
    ];
    tableRows.push(reportData);
  });

  doc.autoTable({
    head: [tableColumn],
    body: tableRows,
    startY: currentY,
    theme: 'grid',
    styles: { fontSize: 10 },
    bodyStyles: { valign: 'top' }
  });

  doc.save(`protocolo-${props.protocol.id}.pdf`);
};

</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
            <v-card>
                <v-tabs v-model="tab" class="bg-violet-100">
                    <v-tab value="one">Visualizar Protocolo</v-tab>
                    <v-tab value="two">Acompanhamentos</v-tab>
                </v-tabs>
                <v-card-text>
                    <v-window v-model="tab">
                        <v-window-item value="one">
                            <form @submit.prevent="submit">
                                <v-card>
                                    <v-container>
                                        <v-select label="Departamento" :items="departments" item-title="name"
                                            item-value="id" variant="outlined" v-model="form.department_id"
                                            @change="form.validate('department_id')"></v-select>
                                        <span v-if="form.invalid('department_id')" class="text-base text-red-500">
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
                                                <v-text-field label="Selecione a data de nascimento:*"
                                                    :model-value="formattedDate" v-bind="props" variant="outlined"
                                                    @update:modelValue="updateDate"></v-text-field>
                                            </template>
                                            <v-date-picker v-model="form.date"
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
                                        <v-textarea v-model="form.description" label="Descrição" variant="outlined"
                                            @change="form.validate('description')"></v-textarea>
                                        <span v-if="form.invalid('description')" class="text-base text-red-500">
                                            {{ form.errors.description }}
                                        </span>
                                    </v-container>

                                    <v-divider></v-divider>
                                    <div class="flex justify-between items-center">
                                        <Link :href="route('protocols.index')"
                                            class="text-base  font-semibold border-2 border-gray-600 rounded-3xl mx-4 px-4 py-1 hover:bg-purple-800 hover:text-white">
                                        Voltar</Link>

                                        <v-btn class="m-4" type="submit" color="primary">Salvar</v-btn>
                                    </div>
                                </v-card>
                            </form>

                            <h1 class="m-4">Pré-visualização dos Arquivos:</h1>
                            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 m-4">
                                <div v-for="attach in protocol.docattachs" :key="attach.id"
                                    class="mb-4 rounded-2xl border-slate-100 border-4 bg-white p-4 md:flex-grow">
                                    <div v-if="isImage(attach.file)">
                                        <!-- {{ attach.file }} -->
                                        <img :src="`../../../storage/${attach.file}`" alt="Image Preview"
                                            class="max-w-full h-auto rounded">

                                    </div>
                                    <div v-else class="flex items-center justify-center">
                                        <iframe :src="`../../../storage/${attach.file}`" class="w-52 h-64 object-cover "
                                            frameborder="0"></iframe>
                                    </div>

                                    <div class="flex justify-between md:flex-row mt-2">
                                        <button @click="downloadFile(attach.file)"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                                            Baixar
                                        </button>
                                        <button @click="deleteFile(attach)"
                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </v-window-item>

                        <v-window-item value="two">
                            <v-dialog max-width="500">
                                <template v-slot:activator="{ props: activatorProps }">
                                    <div class="flex justify-between">
                                        <v-btn v-bind="activatorProps" color="surface-variant" variant="flat"
                                            class="flex justify-end items-end P-8 bg-black rounded-full border-2 border-neutral-500"><svg
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>Acompanhamento
                                        </v-btn>
                                        <v-btn @click="generatePDF" color="surface-variant" variant="flat"
                                            class="flex justify-end items-end p-8 bg-black rounded-full border-2 border-neutral-500">
                                            PDF do Protocolo
                                        </v-btn>
                                    </div>
                                </template>

                                <template v-slot:default="{ isActive }">
                                    <v-card title="Incluir Acompanhamento">
                                        <form @submit.prevent="reportSubmit">
                                            <v-card-text>
                                                <v-container>

                                                    <v-radio-group v-model="formReport.status" inline label="Situação"
                                                        @change="formReport.validate('status')">
                                                        <v-radio label="Aberto" value="A"></v-radio>
                                                        <v-radio label="Em atendimento" value="E"></v-radio>
                                                        <v-radio label="Solucionado" value="S"></v-radio>
                                                    </v-radio-group>
                                                    <span v-if="formReport.errors.status"
                                                        class="text-base text-red-500">
                                                        {{ formReport.errors.status }}
                                                    </span>

                                                    <v-textarea v-model="formReport.description" label="Descrição"
                                                        variant="outlined" maxlength="2000" :counter="2000"
                                                        @change="formReport.validate('description')"></v-textarea>
                                                    <span v-if="formReport.invalid('description')"
                                                        class="text-base text-red-500">
                                                        {{ formReport.errors.description }}
                                                    </span>
                                                </v-container>
                                            </v-card-text>

                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn text="Cancelar" @click="isActive.value = false"></v-btn>
                                                <v-btn class="m-4" type="submit" color="primary">Salvar</v-btn>
                                            </v-card-actions>
                                        </form>
                                    </v-card>
                                </template>
                            </v-dialog>
                            <v-card-text>
                                <h1>Acompanhamento Realizados:</h1>
                                <div v-for="report in reversedReports" :key="report.id" cols="12" md="6">
                                    <v-card class="m-4">
                                        <v-container>
                                            <div class="flex justify-between m-4">
                                                <div>Situação: {{ translateStatus(report.status) }}</div>
                                                <div>Data: {{ formatDate(report.created_at) }}</div>
                                            </div>
                                            <div>{{ report.description }}</div>
                                        </v-container>
                                    </v-card>
                                </div>
                            </v-card-text>

                        </v-window-item>
                    </v-window>
                </v-card-text>
            </v-card>
        </div>
    </AppLayout>
</template>

<style>
.fixed-size-img {
    width: 200px;
    height: 200px;
    object-fit: cover;
}

.v-picker {
    width: 100% !important;
}
</style>