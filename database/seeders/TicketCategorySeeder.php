<?php
namespace Database\Seeders;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Suporte Técnico (Sistema)', 'color' => 'blue'],
            ['name' => 'Dúvidas Médicas / PCMSO', 'color' => 'green'],
            ['name' => 'Financeiro / Faturamento', 'color' => 'yellow'],
            ['name' => 'Agendamentos', 'color' => 'purple'],
            ['name' => 'Outros Assuntos', 'color' => 'gray'],
        ];

        foreach($cats as $cat) {
            TicketCategory::create($cat);
        }
    }
}