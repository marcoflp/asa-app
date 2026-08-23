import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
import "flatpickr/dist/flatpickr.css";
import mask from "@alpinejs/mask";

// Configura o idioma padrão para o calendário
flatpickr.localize(Portuguese);
window.flatpickr = flatpickr;

// Registra o plugin de máscaras no Alpine.js
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(mask);
});

