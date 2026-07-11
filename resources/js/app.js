import flatpickr from "flatpickr";
import { Portuguese } from "flatpickr/dist/l10n/pt.js";
import "flatpickr/dist/flatpickr.css";

// Configura o idioma padrão para o calendário
flatpickr.localize(Portuguese);
window.flatpickr = flatpickr;
