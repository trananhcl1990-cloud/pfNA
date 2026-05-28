(function () {
    const editor = document.getElementById('postEditor');
    const contentInput = document.getElementById('postContent');
    const uploadInput = document.getElementById('postUploadInput');
    const coverInput = document.getElementById('coverImage');
    let uploadMode = 'content';

    document.querySelectorAll('.word-toolbar [data-command]').forEach((button) => {
        button.addEventListener('click', () => {
            const command = button.dataset.command;
            if (command === 'createLink') {
                const url = prompt('Nhập URL:');
                if (url) document.execCommand(command, false, url);
                return;
            }
            document.execCommand(command, false, null);
            editor.focus();
        });
    });

    document.querySelectorAll('.word-toolbar [data-block]').forEach((button) => {
        button.addEventListener('click', () => {
            document.execCommand('formatBlock', false, button.dataset.block);
            editor.focus();
        });
    });

    document.getElementById('insertImage').addEventListener('click', () => {
        uploadMode = 'content';
        uploadInput.click();
    });

    document.getElementById('uploadCover').addEventListener('click', () => {
        uploadMode = 'cover';
        uploadInput.click();
    });

    uploadInput.addEventListener('change', async () => {
        if (!uploadInput.files.length) return;
        const url = await upload(uploadInput.files[0]);
        if (uploadMode === 'cover') {
            coverInput.value = url;
        } else {
            document.execCommand('insertImage', false, url);
            editor.focus();
        }
        uploadInput.value = '';
    });

    document.querySelector('.post-form').addEventListener('submit', () => {
        contentInput.value = editor.innerHTML;
    });

    async function upload(file) {
        const data = new FormData();
        data.append('file', file);

        const response = await fetch(window.cmsRoutes.upload, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.cmsRoutes.csrf,
                'Accept': 'application/json',
            },
            body: data,
        });

        if (!response.ok) {
            throw new Error('Upload failed');
        }

        return (await response.json()).url;
    }
})();
