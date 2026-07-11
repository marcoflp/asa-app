# ASA - Identidade Visual e Design System

Este documento define as diretrizes de design e identidade visual para o aplicativo da Ação Solidária Adventista (ASA), baseando-se no logotipo oficial.

## Paleta de Cores (Color Palette)

As cores principais foram extraídas diretamente do logotipo oficial da ASA para manter a consistência da marca e transmitir seriedade, acolhimento e esperança.

### Cores Primárias da Marca

*   **Verde Escuro (Dark Forest Green)**
    *   **HEX:** `#1a2f26` (Aproximado)
    *   **Uso:** Cor base para elementos estruturais. Ideal para textos de alto contraste (títulos), cabeçalhos principais (headers), barra de navegação (sidebar), e botões primários. Representa a figura esquerda do coração e os textos "AÇÃO" e "ADVENTISTA".
*   **Amarelo Ouro (Golden Yellow)**
    *   **HEX:** `#d6a838` (Aproximado)
    *   **Uso:** Cor de destaque (accent color). Excelente para botões de ação (CTAs) secundários, ícones de destaque, links ativos, badges e elementos que precisam chamar a atenção (como a barra de progresso ou alertas). Representa a figura direita do coração, a chama e o texto "SOLIDÁRIA".

### Cores Neutras (Sugestão para a UI)

Para complementar as cores fortes da marca e manter a interface do sistema limpa, moderna e fácil de usar:

*   **Background (Fundo):** `#f8fafc` (Slate 50) - Um tom quase branco que reduz o cansaço visual.
*   **Cards e Superfícies:** `#ffffff` (Branco puro) - Para criar contraste com o fundo.
*   **Textos Secundários:** `#475569` (Slate 600) ou `#64748b` (Slate 500) - Para descrições, placeholders e textos de menor importância.
*   **Bordas e Divisores:** `#e2e8f0` (Slate 200).

## Tipografia (Typography)

O logotipo original utiliza fontes Serifadas (clássicas), transmitindo tradição e formalidade. No entanto, para um sistema web (painel administrativo), a usabilidade e legibilidade são prioridades.

*   **Fonte Principal (UI, Textos, Menus, Botões):**
    *   Recomendação: **Inter**, **Roboto** ou **Outfit**.
    *   Por que: Fontes sans-serif limpas garantem excelente legibilidade em telas de todos os tamanhos e deixam a interface com cara de "software moderno".
*   **Fonte Secundária (Relatórios PDF e Títulos Grandes - Opcional):**
    *   Recomendação: **Merriweather** ou **Playfair Display**.
    *   Por que: Pode ser usada pontualmente em cabeçalhos de relatórios (como o PDF que acabamos de refatorar) para remeter à elegância e formalidade do logotipo.

## Padrões de Interface (UI Elements)

*   **Botões Principais:** Fundo Verde Escuro (`#1a2f26`) com texto Branco, arredondamento leve (`rounded-lg` ou `rounded-xl`). Efeito de hover escurecendo um pouco mais o verde.
*   **Botões de Destaque / Alertas positivos:** Fundo Amarelo Ouro (`#d6a838`) com texto Verde Escuro ou Preto para garantir acessibilidade e contraste.
*   **Cards e Painéis:** Fundo branco puro com bordas muito sutis (`border border-slate-200`) e sombras leves (`shadow-sm`). O design deve parecer "solto" e não "encaixotado".
*   **Micro-interações:** Todos os botões e links devem ter transições suaves de cor (`transition-colors duration-200`).

## Sugestão de Configuração Tailwind (`tailwind.config.js`)

Para aplicar este design system facilmente no código, adicione a extensão de cores no seu Tailwind:

```javascript
export default {
  theme: {
    extend: {
      colors: {
        asa: {
          green: '#1a2f26',   // Verde escuro oficial
          yellow: '#d6a838',  // Amarelo oficial
          light: '#f0f4f2',   // Fundo esverdeado bem claro (opcional para painéis)
        }
      }
    }
  }
}
```

---
*Este documento deve servir como guia para futuras manutenções e criações de novas telas, garantindo que o sistema da ASA tenha sempre a mesma identidade visual e qualidade profissional.*
