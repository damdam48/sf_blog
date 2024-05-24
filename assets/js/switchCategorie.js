import { sendVisibilityRequest } from "./utils/sendVisibilityRequest";

document.querySelectorAll('input[data-switch-categorie-id]')
    .forEach(input => {
        input.addEventListener('change', (e) => {
            const id = e.currentTarget.dataset.switchCategorieId;
            sendVisibilityRequest(`/admin/categories/${id}/switch`, e.target);
        });
    });