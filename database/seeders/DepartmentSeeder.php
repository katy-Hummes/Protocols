<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Informática',
            'Administração',
            'Recursos Humanos',
            'Assistência Social',
            'Compras e Licitações',
            'Procuradoria Jurídica',
            'Secretaria de Saúde',
            'Procuradoria-Geral',
            'Direitos Humanos',
            'Proteção Animal',
            'Educação e Cultura',
            'Esporte e Lazer',
            'Obras e Viação',
            'Políticas para Mulheres',
            'Agricultura e Meio-Ambiente',
            'Coordenadoria de Cultura, Turismo e Desporto',
        ];

        foreach ($departments as $department) {
            \App\Models\Department::create([
                'name' => $department,
            ]);
        }
    }
}
