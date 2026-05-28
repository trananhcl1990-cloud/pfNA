<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa trực tiếp website</title>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="/site/css/style.css?v=6">
    <link rel="stylesheet" href="/cms-admin-assets/cms.css?v=8">
</head>
<body class="page-editor-body">
    <div class="editor-toolbar">
        <a href="{{ route('admin.dashboard') }}">← Admin</a>
        <button type="button" data-command="bold">B</button>
        <button type="button" data-command="italic"><i>I</i></button>
        <button type="button" data-command="createLink">Link</button>
        <button type="button" id="replaceMediaBtn">Đổi ảnh/video</button>
        <button type="button" id="replaceCvBtn">Đổi file CV từ máy tính</button>
        <span id="editorStatus">Bấm vào text để sửa. Bấm vào icon rồi chọn Đổi ảnh/video để upload ảnh thay icon.</span>
        <button type="button" class="admin-primary" id="savePageBtn">Lưu website</button>
    </div>
    <input type="file" id="mediaInput" hidden accept="image/*,video/*,.pdf">
    <input type="file" id="cvInput" hidden accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
    <main id="editablePage" class="editable-page">
        {!! $pageHtml !!}
    </main>
    <script>
        window.cmsRoutes = {
            savePage: @json(route('admin.page.update')),
            upload: @json(route('admin.upload')),
            uploadCv: @json(route('admin.cv.upload')),
            csrf: @json(csrf_token()),
        };
    </script>
    <script src="/site/js/script.js?v=2"></script>
    <script src="/cms-admin-assets/page-editor.js?v=10"></script>
</body>
</html>
