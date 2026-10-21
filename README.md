# ASA App - Ação Solidária Adventista (Passo Fundo)

Este é um sistema desenvolvido para auxiliar e otimizar o trabalho da **Ação Solidária Adventista (ASA)** em Passo Fundo/RS. O objetivo principal é gerenciar o atendimento a beneficiários, o controle de estoque de doações e o registro de entregas (retiradas).

## 🚀 Funcionalidades

- **📊 Dashboard Inteligente**: Visão geral de atendimentos, produtos mais retirados e métricas de impacto por período (diário, semanal, mensal).
- **👥 Gestão de Beneficiários**:
    - Cadastro completo com dados pessoais e endereço.
    - Composição familiar detalhada (incluindo idade de filhos).
    - Acompanhamento de programas sociais (Bolsa Família, etc.).
    - Registro de interesse e acompanhamento de estudos bíblicos.
- **📦 Controle de Produtos**: Catálogo de itens disponíveis para doação (alimentos, roupas, higiene, etc.) com unidades de medida e categorias.
- **📝 Registro de Retiradas**: Monitoramento preciso de quais itens cada beneficiário recebeu, com data e histórico completo.
- **🔐 Autenticação Segura**: Gerenciamento de usuários com suporte a autenticação de dois fatores (2FA).

## 🛠️ Tecnologias Utilizadas

- **Framework**: [Laravel 13](https://laravel.com)
- **Frontend**: [Livewire 4](https://livewire.laravel.com)
- **Componentes UI**: [Flux UI](https://fluxui.dev)
- **Linguagem**: PHP 8.3+
- **Estilização**: Tailwind CSS
- **Painel inicial**: [Angular 22](https://angular.dev) (componentes standalone, signals, zoneless, `httpResource`) consumindo uma API JSON do Laravel

## 📋 Requisitos

- PHP >= 8.3
- Composer
- Node.js & NPM
- SQLite (ou outro banco de dados compatível com Laravel)

## 🔧 Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/marcoflp/asa-app.git
   cd asa-app
   ```

2. Instale as dependências do PHP:
   ```bash
   composer install
   ```

3. Instale as dependências do Frontend:
   ```bash
   npm install
   ```

4. Configure o arquivo de ambiente:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Execute as migrações (o SQLite será criado automaticamente se configurado):
   ```bash
   php artisan migrate
   ```

6. Inicie o servidor de desenvolvimento:
   ```bash
   # Inicia servidor PHP, Vite e Queue Listener simultaneamente
   npm run dev
   ```

## 📊 Painel inicial (Angular)

O painel da tela inicial é um app Angular que roda dentro do layout do Laravel como um *custom element* (`<asa-painel>`), usando a mesma sessão de login.

- **API (Laravel)**: `GET /api/painel/atencao`, `GET /api/painel/impacto?periodo=30-dias&categoria=` e `GET /api/painel/relatorio` (PDF). A lógica fica em `app/Painel` (`Periodo`, `Intervalo`, `ImpactoDoPeriodo`, `PontosDeAtencao`).
- **App (Angular)**: código em `painel/`, compilado para `public/painel`.

```bash
cd painel
npm install
npm test -- --watch=false   # testes (Vitest)
npm run build               # gera public/painel
```

No deploy, o GitHub Actions roda os testes, compila o painel e envia para o servidor.

## 🤝 Contribuição

Este projeto visa facilitar o trabalho voluntário. Sinta-se à vontade para abrir Issues ou enviar Pull Requests com melhorias.

---

Desenvolvido para a **ASA - Ação Solidária Adventista de Passo Fundo**.
