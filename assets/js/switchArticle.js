import { sendVisibilityRequest } from './sendVisibilityRequest';
document.querySelectorAll('input[data-switch-article-id]')
    .forEach(input => {
        input.addEventListener('change', async (e) => {
            const id = e.currentTarget.dataset.switchArticleId;
            sendVisibilityRequest(`/admin/articles/${id}/switch`, e.target);

        });
    });