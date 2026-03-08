<?php
$pageTitle = 'Manage Announcements';
require_once __DIR__ . '/../../models/Announcement.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/MailService.php';
include __DIR__ . '/../../includes/admin-layout-header.php';

$announcementModel = new Announcement();
$userModel = new User();
$mailService = new MailService();
$message = '';
$error = '';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($announcementModel->delete($id)) {
        $message = 'Announcement deleted successfully!';
    } else {
        $error = 'Failed to delete announcement.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $title = sanitize($_POST['title']);
    $content = $_POST['content']; // Allow HTML from Quill
    $image_path = null;
    $id = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../../public/uploads/announcements/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($file_extension, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/announcements/" . $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
        }
    }

    if (empty($title) || empty(trim(strip_tags($content)))) {
        $error = 'Title and content cannot be empty.';
    } elseif (!$error) {
        if ($_POST['action'] === 'create') {
            if ($announcementModel->create($title, $content, $image_path)) {
                $message = 'Announcement created successfully!';
            } else {
                $error = 'Failed to create announcement.';
            }
        } elseif ($_POST['action'] === 'update' && $id) {
            if ($announcementModel->update($id, $title, $content, $image_path)) {
                $message = 'Announcement updated successfully!';
            } else {
                $error = 'Failed to update announcement.';
            }
        }
    }
}

$announcements = $announcementModel->getAll();
?>

<?php if ($message): ?>
    <div class="p-6 mb-8 text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center shadow-lg shadow-emerald-100/50 animate-float" role="alert">
        <i class="fas fa-check-circle mr-4 text-2xl"></i>
        <span class="font-bold text-lg"><?php echo $message; ?></span>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="p-6 mb-8 text-red-800 bg-red-50 rounded-xl border border-red-100 flex items-center shadow-lg shadow-red-100/50" role="alert">
        <i class="fas fa-exclamation-circle mr-4 text-2xl"></i>
        <span class="font-bold text-lg"><?php echo $error; ?></span>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
    <!-- Creation Form -->
    <div class="lg:col-span-12 xl:col-span-5">
        <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/50 border border-white overflow-hidden sticky top-28">
            <div class="bg-slate-900 px-8 py-6 flex items-center justify-between">
                <h3 class="text-xl font-black text-white font-heading tracking-tight">
                    <i class="fas fa-bullhorn mr-3 text-primary-400"></i>Create Announcement
                </h3>
            </div>
            
            <div class="p-8">
                <form method="POST" id="announcementForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="announcement_id" id="announcementId" value="">
                    
                    <div class="mb-6">
                        <label for="title" class="block text-gray-700 text-xs font-black uppercase tracking-[0.2em] mb-3 ml-1">Announcement Title</label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary-100 focus:border-primary-500 transition-all font-bold text-gray-800 placeholder-slate-400"
                            placeholder="e.g. System Maintenance Notice">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-xs font-black uppercase tracking-[0.2em] mb-3 ml-1">Featured Image (Optional)</label>
                        <div class="flex items-center justify-center w-full">
                            <label for="image-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                                    <p class="text-xs font-bold text-slate-500">Click to upload or drag and drop</p>
                                    <p id="file-name" class="text-[10px] text-primary-600 font-black mt-2 hidden"></p>
                                </div>
                                <input id="image-upload" type="file" name="image" class="hidden" accept="image/*" onchange="updateFileName(this)" />
                            </label>
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-gray-700 text-xs font-black uppercase tracking-[0.2em] mb-3 ml-1">Content</label>
                        <input type="hidden" name="content" id="hiddenContent">
                        <div id="editor-container" class="bg-white rounded-xl border border-slate-200 shadow-inner" style="height: 250px;"></div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" id="submitBtn" class="flex-1 bg-slate-900 hover:bg-black text-white font-black py-5 rounded-xl shadow-xl shadow-slate-200 transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-3"></i>Post Announcement
                        </button>
                        <button type="button" id="cancelBtn" onclick="resetForm()" class="hidden bg-slate-200 hover:bg-slate-300 text-slate-700 font-black py-5 px-8 rounded-xl transition transform hover:-translate-y-1 active:scale-95 flex items-center justify-center">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Announcement List -->
    <div class="lg:col-span-12 xl:col-span-7">
        <div class="bg-white rounded-2xl shadow-2xl shadow-slate-200/50 border border-white overflow-hidden">
            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900 font-heading tracking-tight">Existing Posts</h3>
                <span class="px-3 py-1 bg-primary-100 text-primary-700 text-[10px] font-black uppercase tracking-widest rounded-full">
                    <?php echo count($announcements); ?> Total
                </span>
            </div>
            
            <div class="divide-y divide-slate-100">
                <?php if (empty($announcements)): ?>
                    <div class="p-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-bullhorn text-slate-300 text-3xl"></i>
                        </div>
                        <h4 class="text-lg font-black text-slate-400">No announcements yet</h4>
                        <p class="text-slate-400 text-sm mt-1">Start by creating your first post</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($announcements as $a): ?>
                        <div class="p-8 hover:bg-slate-50 transition-colors group">
                            <div class="flex gap-6">
                                <?php if ($a['image_path']): ?>
                                    <div class="w-24 h-24 rounded-xl overflow-hidden shrink-0 shadow-lg">
                                        <img src="<?php echo BASE_URL . '/' . $a['image_path']; ?>" class="w-full h-full object-cover" alt="">
                                    </div>
                                <?php else: ?>
                                    <div class="w-24 h-24 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                        <i class="fas fa-image text-slate-300 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start gap-4 mb-2">
                                        <h4 class="text-lg font-black text-slate-900 leading-tight"><?php echo $a['title']; ?></h4>
                                        <div class="flex items-center bg-white shadow-sm border border-slate-100 rounded-xl p-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0 shrink-0">
                                            <button type="button" 
                                                onclick="editAnnouncement(<?php echo htmlspecialchars(json_encode($a)); ?>)"
                                                class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all">
                                                <i class="fas fa-edit text-sm"></i>
                                            </button>
                                            <a href="?delete=<?php echo $a['id']; ?>" onclick="return confirm('Permanent delete this announcement?')" 
                                                class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                                <i class="fas fa-trash-alt text-sm"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="text-slate-500 text-sm line-clamp-2 prose-sm mb-4">
                                        <?php echo strip_tags($a['content']); ?>
                                    </div>
                                    <div class="flex items-center text-[10px] font-black tracking-widest uppercase text-slate-400">
                                        <i class="far fa-calendar-alt mr-2"></i>
                                        <?php echo date('M d, Y h:i A', strtotime($a['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quill JS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Compose your announcement content...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'clean']
            ]
        }
    });

    document.getElementById('announcementForm').onsubmit = function() {
        document.getElementById('hiddenContent').value = quill.root.innerHTML;
        return true;
    };

    function updateFileName(input) {
        const fileNameDisplay = document.getElementById('file-name');
        if (input.files && input.files.length > 0) {
            fileNameDisplay.innerText = input.files[0].name;
            fileNameDisplay.classList.remove('hidden');
        } else {
            fileNameDisplay.classList.add('hidden');
        }
    }

    function editAnnouncement(announcement) {
        document.getElementById('formAction').value = 'update';
        document.getElementById('announcementId').value = announcement.id;
        document.getElementById('title').value = announcement.title;
        quill.root.innerHTML = announcement.content;
        
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-3"></i>Update Announcement';
        document.getElementById('cancelBtn').classList.remove('hidden');
        
        // Scroll to form
        document.getElementById('announcementForm').scrollIntoView({ behavior: 'smooth' });
    }

    function resetForm() {
        document.getElementById('announcementForm').reset();
        document.getElementById('formAction').value = 'create';
        document.getElementById('announcementId').value = '';
        quill.root.innerHTML = '';
        
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-paper-plane mr-3"></i>Post Announcement';
        document.getElementById('cancelBtn').classList.add('hidden');
        document.getElementById('file-name').classList.add('hidden');
    }
</script>

<?php include __DIR__ . '/../../includes/admin-layout-footer.php'; ?>
