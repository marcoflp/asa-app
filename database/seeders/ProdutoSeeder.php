<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            // Alimentos
            ['nome' => 'Arroz',           'categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Feijão',          'categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Macarrão',        'categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Óleo de Soja',    'categoria' => 'Alimento', 'unidade' => 'litro'],
            ['nome' => 'Açúcar',          'categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Sal',             'categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Farinha de Trigo','categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Leite em Pó',     'categoria' => 'Alimento', 'unidade' => 'kg'],
            ['nome' => 'Extrato de Tomate','categoria' => 'Alimento','unidade' => 'unidade'],
            ['nome' => 'Sardinha',        'categoria' => 'Alimento', 'unidade' => 'lata'],

            // Higiene Pessoal
            ['nome' => 'Sabonete',        'categoria' => 'Higiene',  'unidade' => 'unidade'],
            ['nome' => 'Shampoo',         'categoria' => 'Higiene',  'unidade' => 'frasco'],
            ['nome' => 'Condicionador',   'categoria' => 'Higiene',  'unidade' => 'frasco'],
            ['nome' => 'Creme Dental',    'categoria' => 'Higiene',  'unidade' => 'unidade'],
            ['nome' => 'Escova de Dente', 'categoria' => 'Higiene',  'unidade' => 'unidade'],
            ['nome' => 'Desodorante',     'categoria' => 'Higiene',  'unidade' => 'unidade'],
            ['nome' => 'Papel Higiênico', 'categoria' => 'Higiene',  'unidade' => 'rolo'],
            ['nome' => 'Absorvente',      'categoria' => 'Higiene',  'unidade' => 'pacote'],
            ['nome' => 'Fralda',          'categoria' => 'Higiene',  'unidade' => 'pacote'],

            // Limpeza
            ['nome' => 'Detergente',      'categoria' => 'Limpeza',  'unidade' => 'frasco'],
            ['nome' => 'Sabão em Pó',     'categoria' => 'Limpeza',  'unidade' => 'kg'],
            ['nome' => 'Água Sanitária',  'categoria' => 'Limpeza',  'unidade' => 'litro'],
            ['nome' => 'Desinfetante',    'categoria' => 'Limpeza',  'unidade' => 'frasco'],

            // Vestuário
            ['nome' => 'Cobertor',        'categoria' => 'Vestuário','unidade' => 'unidade'],
            ['nome' => 'Agasalho',        'categoria' => 'Vestuário','unidade' => 'peça'],
            ['nome' => 'Meia',            'categoria' => 'Vestuário','unidade' => 'par'],
            ['nome' => 'Roupa',           'categoria' => 'Vestuário','unidade' => 'peça'],
        ];

        foreach ($produtos as $produto) {
            Produto::create(array_merge(['estoque' => 0], $produto));
        }
    }
}
