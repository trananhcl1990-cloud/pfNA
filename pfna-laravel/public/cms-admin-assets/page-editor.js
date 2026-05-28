(function () {
    const page = document.getElementById('editablePage');
    const status = document.getElementById('editorStatus');
    const fileInput = document.getElementById('mediaInput');
    const cvInput = document.getElementById('cvInput');
    let selectedMedia = null;
    let selectedIcon = null;
    let saving = false;

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
            status.textContent = 'Dang sua text. Bam Luu website khi hoan tat.';
        });

        element.addEventListener('input', () => {
            syncTranslationAttribute(element);
        });

        element.addEventListener('blur', () => {
            syncTranslationAttribute(element);
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

            const href = prompt('Nhap link moi:', link.getAttribute('href') || '');
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
                const url = prompt('Nhap URL:');
                if (url) document.execCommand(command, false, url);
                return;
            }
            document.execCommand(command, false, null);
        });
    });

    document.getElementById('replaceMediaBtn').addEventListener('click', () => {
        if (!selectedMedia && !selectedIcon) {
            status.textContent = 'Hay bam chon anh, video hoac icon truoc.';
            return;
        }

        fileInput.click();
    });

    document.getElementById('replaceCvBtn').addEventListener('click', () => {
        cvInput.click();
    });

    fileInput.addEventListener('change', async () => {
        if (!fileInput.files.length) return;

        try {
            const file = fileInput.files[0];
            const url = await upload(file);

            if (selectedIcon) {
                if (!file.type.startsWith('image/')) {
                    status.textContent = 'Icon chi doi duoc bang file anh.';
                    fileInput.value = '';
                    return;
                }

                const image = document.createElement('img');
                image.setAttribute('src', url);
                image.setAttribute('alt', selectedIcon.getAttribute('name') || 'Icon');
                image.setAttribute('loading', 'lazy');
                image.className = 'cms-replaceable-icon';
                selectedIcon.replaceWith(image);
                bindMediaPicker(image);
                selectedIcon = null;
                selectedMedia = image;
                image.classList.add('cms-selected-media');
                status.textContent = 'Da doi icon thanh anh. Dang tu dong luu...';
                await savePage({ reload: false });
                fileInput.value = '';
                return;
            }

            if (selectedMedia) {
                selectedMedia.setAttribute('src', url);
                if (selectedMedia.closest('.category-icon, .method-icon, .tech-item')) {
                    selectedMedia.classList.add('cms-replaceable-icon');
                }
                status.textContent = 'Da doi media. Dang tu dong luu...';
                await savePage({ reload: false });
            }
        } catch (error) {
            status.textContent = 'Upload hoac luu that bai: ' + error.message;
        } finally {
            fileInput.value = '';
        }
    });

    cvInput.addEventListener('change', async () => {
        if (!cvInput.files.length) return;

        try {
            status.textContent = 'Dang upload file CV tu may tinh...';
            const result = await uploadCv(cvInput.files[0]);
            const cvLink = findCvLink();

            if (cvLink) {
                cvLink.setAttribute('href', result.url);
            }

            status.textContent = 'Da doi file CV: ' + result.name + '. Dang tu dong luu...';
            await savePage({ reload: false });
        } catch (error) {
            status.textContent = 'Doi file CV that bai: ' + error.message;
        } finally {
            cvInput.value = '';
        }
    });

    document.getElementById('savePageBtn').addEventListener('click', () => {
        savePage({ reload: true });
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

    async function savePage(options = {}) {
        if (saving) return;
        saving = true;
        status.textContent = 'Dang luu website...';

        const cleanup = prepareForSave();

        try {
            const response = await fetch(window.cmsRoutes.savePage, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.cmsRoutes.csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ html: page.innerHTML }),
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            status.textContent = 'Da luu website.';
            if (options.reload) {
                window.setTimeout(() => window.location.reload(), 500);
            } else {
                cleanup();
            }
        } catch (error) {
            cleanup();
            status.textContent = 'Luu that bai: ' + error.message + '. Hay dang nhap lai admin neu bi 419.';
            throw error;
        } finally {
            saving = false;
        }
    }

    function prepareForSave() {
        syncAllTranslationAttributes();

        const editableElements = Array.from(page.querySelectorAll('[contenteditable]'));
        const selectedMediaElements = Array.from(page.querySelectorAll('.cms-selected-media'));
        const selectedIconElements = Array.from(page.querySelectorAll('.cms-selected-icon'));

        editableElements.forEach((element) => {
            element.removeAttribute('contenteditable');
            element.classList.remove('cms-editable-text');
        });
        selectedMediaElements.forEach((element) => element.classList.remove('cms-selected-media'));
        selectedIconElements.forEach((element) => element.classList.remove('cms-selected-icon'));
        page.querySelectorAll('.typing-text').forEach((element) => {
            element.textContent = '';
        });
        cleanPastedFormatting();

        return () => {
            editableElements.forEach((element) => {
                element.setAttribute('contenteditable', 'true');
                element.classList.add('cms-editable-text');
            });
            selectedMediaElements.forEach((element) => element.classList.add('cms-selected-media'));
            selectedIconElements.forEach((element) => element.classList.add('cms-selected-icon'));
        };
    }

    function getCurrentLanguage() {
        const stored = localStorage.getItem('portfolio-language');
        if (['vi', 'en', 'zh'].includes(stored)) {
            return stored;
        }

        const htmlLang = document.documentElement.lang;
        if (htmlLang === 'zh-CN') {
            return 'zh';
        }

        return ['vi', 'en', 'zh'].includes(htmlLang) ? htmlLang : 'vi';
    }

    function syncAllTranslationAttributes() {
        page.querySelectorAll('[contenteditable]').forEach(syncTranslationAttribute);
    }

    function syncTranslationAttribute(element) {
        const lang = getCurrentLanguage();
        const attr = 'data-' + lang;

        if (!element.hasAttribute(attr)) {
            return;
        }

        element.setAttribute(attr, getEditableText(element));
    }

    function getEditableText(element) {
        if (element.tagName === 'BR') {
            return '';
        }

        return (element.innerText || element.textContent || '').trim();
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
        status.textContent = 'Da chon media. Bam Doi anh/video de upload file moi.';
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
        status.textContent = 'Da chon icon. Bam Doi anh/video de upload anh thay icon.';
    }

    async function upload(file) {
        const data = new FormData();
        data.append('file', file);

        const response = await fetch(window.cmsRoutes.upload, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': window.cmsRoutes.csrf,
                'Accept': 'application/json',
            },
            body: data,
        });

        if (!response.ok) {
            throw new Error('Upload HTTP ' + response.status);
        }

        return (await response.json()).url;
    }

    async function uploadCv(file) {
        const data = new FormData();
        data.append('cv_file', file);

        const response = await fetch(window.cmsRoutes.uploadCv, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': window.cmsRoutes.csrf,
                'Accept': 'application/json',
            },
            body: data,
        });

        if (!response.ok) {
            throw new Error('CV upload HTTP ' + response.status);
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
