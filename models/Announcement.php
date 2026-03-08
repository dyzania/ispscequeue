<?php
require_once __DIR__ . '/../config/config.php';

class Announcement {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Get all announcements ordered by newest first for an office.
    public function getAll($officeId = null) {
        $officeId = $officeId ?? ($_SESSION['office_id'] ?? 1);
        $stmt = $this->db->prepare("SELECT * FROM announcements WHERE office_id = ? ORDER BY created_at DESC");
        $stmt->execute([$officeId]);
        return $stmt->fetchAll();
    }
    
    // Get a single announcement by ID.
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /** Create a new announcement.
     * @param string $title
     * @param string $content
     * @param string|null $image_path */

    public function create($title, $content, $image_path = null, $officeId = 1) {
        $stmt = $this->db->prepare("INSERT INTO announcements (title, content, image_path, office_id) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $content, $image_path, $officeId]);
    }

    /** Update an existing announcement.
     * @param int $id
     * @param string $title
     * @param string $content
     * @param string|null $image_path */
    public function update($id, $title, $content, $image_path = null) {
        if ($image_path) {
            // Delete old image if it exists and is different
            $old = $this->getById($id);
            if ($old && $old['image_path'] && $old['image_path'] !== $image_path) {
                $full_path = __DIR__ . '/../public/' . $old['image_path'];
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
            }
            $stmt = $this->db->prepare("UPDATE announcements SET title = ?, content = ?, image_path = ? WHERE id = ?");
            return $stmt->execute([$title, $content, $image_path, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
            return $stmt->execute([$title, $content, $id]);
        }
    }
    
    /**Delete an announcement.
     * @param int $id */

    public function delete($id) {
        $announcement = $this->getById($id);
        if ($announcement && $announcement['image_path']) {
            $full_path = __DIR__ . '/../public/' . $announcement['image_path'];
            if (file_exists($full_path)) {
                unlink($full_path);
            }
        }
        
        $stmt = $this->db->prepare("DELETE FROM announcements WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getLatestId() {
        $stmt = $this->db->query("SELECT MAX(id) as max_id FROM announcements");
        $result = $stmt->fetch();
        return $result['max_id'] ?: 0;
    }

    public function getUnreadCount($userId, $officeId = null) {
        $officeId = $officeId ?? ($_SESSION['office_id'] ?? 1);
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as unread_count 
            FROM announcements 
            WHERE office_id = ? AND id > (SELECT last_read_announcement_id FROM users WHERE id = ?)
        ");
        $stmt->execute([$officeId, $userId]);
        $result = $stmt->fetch();
        return $result['unread_count'] ?: 0;
    }

    public function markAsRead($userId) {
        $latestId = $this->getLatestId();
        if ($latestId > 0) {
            $stmt = $this->db->prepare("UPDATE users SET last_read_announcement_id = ? WHERE id = ?");
            return $stmt->execute([$latestId, $userId]);
        }
        return true;
    }
}
?>
