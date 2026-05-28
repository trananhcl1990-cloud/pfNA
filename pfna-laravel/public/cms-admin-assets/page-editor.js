(function () {
    const page = document.getElementById('editablePage');
    const status = document.getElementById('editorStatus');
    const fileInput = document.getElementById('mediaInput');
    const cvInput = document.getElementById('cvInput');
    let selectedMedia = null;
    let selectedIcon = null;

    const editableSelector = 'h1,h2,h3,h4,h5,h6,p,li,blockquote,span,label,a,figcaption,td,th';
    const skipSelector = [
        '.editor-toolbar',
        '.language-switcher',
        '.nav-toggle',
        '.back-to-top',
        '.typing-container',
        '.typing-text',
        '.cursor',
        '.particles-bg',
        '.scroll-indicator',
        'script',
        'style',
        'input',
        'textarea',
        'select',
    ].join(',');

    page.querySelectorAll(editableSelector).forEach((element) => {
        if (element.closest(skipSelector) || hasEditableChild(element) || !hasOwnText(element)) {
            return;
        }

        element.setAttribute('contenteditable', 'true');
        element.classList.add('cms-editable-text');

        element.addEventListener('focus', () => {
            status.textContent = 'Đang sửa text. Bấm Lưu website khi hoàn tất.';
        });

        element.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        element.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' && isInlineEdit(element)) {
                event.preventDefault();
                element.blur();
            }
        });

        element.addEventListener('paste', (event) => {
            event.preventDefault();
            const text = event.clipboardData?.getData('text/plain') || '';
            document.execCommand('insertText', false, text);
        });
    });

    page.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey) {
                return;
            }
            event.preventDefault();
        });

        link.addEventListener('dblclick', () => {
            if (link.isContentEditable) {
                return;
            }

            const href = prompt('Nhập link mới:', link.getAttribute('href') || '');
            if (href !== null) {
                link.setAttribute('href', href);
            }
        });
    });

    page.querySelectorAll('img,video').forEach(bindMediaPicker);
    page.querySelectorAll('ion-icon').forEach(bindIconPicker);

    document.querySelectorAll('[data-command]').forEach((button) => {
        button.addEventListener('click', () => {
            const command = button.dataset.command;
            if (command === 'createLink') {
                const url = prompt('Nhập URL:');
                if (url) document.execCommand(command, false, url);
                return;
            }
            document.execCommand(command, false, null);
        });
    });

    document.getElementById('replaceMediaBtn').addEventListener('click', () => {
        if (!selectedMedia && !selectedIcon) {
            status.textContent = 'Hãy bấm chọn một ảnh, video hoặc icon trước.';
            return;
        }

        fileInput.click();
    });

    document.getElementById('replaceIconBtn').addEventListener('click', () => {
        if (!selectedIcon) {
            status.textContent = 'Hãy bấm chọn một icon trước.';
            return;
        }

        const currentName = selectedIcon.getAttribute('name') || '';
        const nextName = prompt('Nhập tên icon mới:', currentName);
        if (!nextName) {
            return;
        }

        selectedIcon.setAttribute('name', nextName.trim());
        status.textContent = 'Đã đổi icon bằng tên. Bấm Lưu website để ghi lại.';
    });

    document.getElementById('replaceCvBtn').addEventListener('click', () => {
        cvInput.click();
    });

    fileInput.addEventListener('change', async () => {
        if (!fileInput.files.length) return;

        const file = fileInput.files[0];
        const url = await upload(file);

        if (selectedIcon) {
            if (!file.type.startsWith('image/')) {
                status.textContent = 'Icon chỉ đổi được bằng file ảnh.';
                fileInput.value = '';
                return;
            }

            const image = document.createElement('img');
            image.setAttribute('src', url);
            image.setAttribute('alt', selectedIcon.getAttribute('name') || 'Icon');
            image.className = 'cms-replaceable-icon';
            selectedIcon.replaceWith(image);
            bindMediaPicker(image);
            selectedIcon = null;
            selectedMedia = image;
            image.classList.add('cms-selected-media');
            status.textContent = 'Đã đổi icon thành ảnh. Bấm Lưu website để ghi lại.';
            fileInput.value = '';
            return;
        }

        if (selectedMedia) {
            selectedMedia.setAttribute('src', url);
            status.textContent = 'Đã đổi media. Bấm Lưu website để ghi lại.';
        }

        fileInput.value = '';
    });

    cvInput.addEventListener('change', async () => {
        if (!cvInput.files.length) return;

        status.textContent = 'Đang upload file CV từ máy tính...';
        const result = await uploadCv(cvInput.files[0]);
        const cvLink = findCvLink();

        if (cvLink) {
            cvLink.setAttribute('href', result.url);
        }

        status.textContent = 'Đã đổi file CV: ' + result.name + '. Nút Tải CV sẽ dùng file mới.';
        cvInput.value = '';
    });

    document.getElementById('savePageBtn').addEventListener('click', async () => {
        page.querySelectorAll('[contenteditable]').forEach((element) => {
            element.removeAttribute('contenteditable');
            element.classList.remove('cms-editable-text');
        });
        page.querySelectorAll('.cms-selected-media').forEach((element) => {
            element.classList.remove('cms-selected-media');
        });
        page.querySelectorAll('.cms-selected-icon').forEach((element) => {
            element.classList.remove('cms-selected-icon');
        });
        page.querySelectorAll('.typing-text').forEach((element) => {
            element.textContent = '';
        });
        cleanPastedFormatting();

        const response = await fetch(window.cmsRoutes.savePage, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.cmsRoutes.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ html: page.innerHTML }),
        });

        if (!response.ok) {
            status.textContent = 'Lưu thất bại. Hãy thử lại.';
            return;
        }

        status.textContent = 'Đã lưu website.';
        window.setTimeout(() => window.location.reload(), 700);
    });

    function bindMediaPicker(media) {
        media.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            selectMedia(media);
        });
    }

    function bindIconPicker(icon) {
        if (icon.closest(skipSelector)) {
            return;
        }

        icon.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            selectIcon(icon);
        });
    }

    function hasEditableChild(element) {
        return Array.from(element.children).some((child) => {
            return child.matches(editableSelector) || child.querySelector(editableSelector);
        });
    }

    function hasOwnText(element) {
        return Array.from(element.childNodes).some((node) => {
            return node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== '';
        });
    }

    function isInlineEdit(element) {
        return ['SPAN', 'A', 'LABEL'].includes(element.tagName);
    }

    function selectMedia(media) {
        page.querySelectorAll('.cms-selected-media').forEach((element) => {
            element.classList.remove('cms-selected-media');
        });
        page.querySelectorAll('.cms-selected-icon').forEach((element) => {
            element.classList.remove('cms-selected-icon');
        });
        selectedMedia = media;
        selectedIcon = null;
        media.classList.add('cms-selected-media');
        status.textContent = 'Đã chọn media. Bấm Đổi ảnh/video để upload file mới.';
    }

    function selectIcon(icon) {
        page.querySelectorAll('.cms-selected-media').forEach((element) => {
            element.classList.remove('cms-selected-media');
        });
        page.querySelectorAll('.cms-selected-icon').forEach((element) => {
            element.classList.remove('cms-selected-icon');
        });
        selectedIcon = icon;
        selectedMedia = null;
        icon.classList.add('cms-selected-icon');
        status.textContent = 'Đã chọn icon "' + (icon.getAttribute('name') || '') + '". Bấm Đổi ảnh/video để upload ảnh thay icon, hoặc Đổi icon bằng tên.';
    }

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

    async function uploadCv(file) {
        const data = new FormData();
        data.append('cv_file', file);

        const response = await fetch(window.cmsRoutes.uploadCv, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.cmsRoutes.csrf,
                'Accept': 'application/json',
            },
            body: data,
        });

        if (!response.ok) {
            throw new Error('CV upload failed');
        }

        return response.json();
    }

    function findCvLink() {
        return page.querySelector('a.btn-primary[download]') || page.querySelector('a[download]');
    }

    function cleanPastedFormatting() {
        page.querySelectorAll('[style]').forEach((element) => {
            const style = element.getAttribute('style') || '';
            if (style.includes('Segoe UI') || style.includes('background-color: rgb(240, 240, 240)')) {
                element.removeAttribute('style');
            }
        });

        page.querySelectorAll('span').forEach((span) => {
            if (!span.attributes.length && span.parentNode) {
                span.replaceWith(document.createTextNode(span.textContent || ''));
            }
        });
    }
})();
