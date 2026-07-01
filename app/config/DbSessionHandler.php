<?php
class DbSessionHandler implements SessionHandlerInterface
{
    private mysqli $koneksi;

    public function __construct(mysqli $koneksi)
    {
        $this->koneksi = $koneksi;
    }

    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id): string|false
    {
        $stmt = mysqli_prepare($this->koneksi, "SELECT session_data FROM tb_sessions WHERE session_id = ? LIMIT 1");
        if (!$stmt) {
            error_log("[SESSION-DB] read prepare failed: " . mysqli_error($this->koneksi));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if ($row && isset($row['session_data']) && $row['session_data'] !== null) {
            error_log("[SESSION-DB] read id=$id found data");
            $decoded = base64_decode($row['session_data'], true);
            return $decoded === false ? '' : $decoded;
        }

        error_log("[SESSION-DB] read id=$id NOT FOUND");
        return '';
    }

    public function write($id, $data): bool
    {
        $now = time();
        $encoded = base64_encode($data);

        $stmt = mysqli_prepare(
            $this->koneksi,
            "INSERT INTO tb_sessions (session_id, session_data, last_access)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE session_data = ?, last_access = ?"
        );

        if (!$stmt) {
            error_log("[SESSION-DB] write prepare failed: " . mysqli_error($this->koneksi));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssisi', $id, $encoded, $now, $encoded, $now);
        $success = mysqli_stmt_execute($stmt);

        if (!$success) {
            error_log("[SESSION-DB] write execute failed: " . mysqli_stmt_error($stmt));
        } else {
            error_log("[SESSION-DB] write id=$id success, data_len=" . strlen($data));
        }

        mysqli_stmt_close($stmt);
        return $success;
    }

    public function destroy($id): bool
    {
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM tb_sessions WHERE session_id = ?");
        if (!$stmt) {
            error_log("[SESSION-DB] destroy prepare failed: " . mysqli_error($this->koneksi));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 's', $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    public function gc($max_lifetime): int|false
    {
        $threshold = time() - $max_lifetime;
        $stmt = mysqli_prepare($this->koneksi, "DELETE FROM tb_sessions WHERE last_access < ?");
        if (!$stmt) {
            error_log("[SESSION-DB] gc prepare failed: " . mysqli_error($this->koneksi));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'i', $threshold);
        mysqli_stmt_execute($stmt);
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected;
    }
}