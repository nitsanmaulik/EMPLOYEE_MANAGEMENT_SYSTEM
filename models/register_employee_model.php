<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class RegisterEmployeeModel {
    private $conn;
    private $mailer;

    public function __construct($connection) {
        $this->conn = $connection;
        $this->initializeMailer();
    }

    private function initializeMailer() {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'maulikkikani.nitsan@gmail.com';
        $this->mailer->Password = 'megi ytyu egfo ntpy';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->SMTPDebug = 0;
    }

    public function registerEmployee($data, $file = null) {
        //$this->validateRegistrationData($data);
        $photoPath = $this->handlePhotoUpload($file);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $this->insertEmployee(
            $data['name'],
            $data['email'],
            $hashedPassword,
            $data['phone'],
            $data['qualification'],
            $data['role'],
            $photoPath
        );

        $this->sendWelcomeEmail($data['email'], $data['name'], $data['password']);
    }

    
    private function handlePhotoUpload($file) {
        if (empty($file['name'])) {
            return null;
        }

        $uploadDir = "../Assets/Images/";
        $photoName = time() . "_" . basename($file['name']);
        $targetFile = $uploadDir . $photoName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png"];

        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception("Only JPG, JPEG, and PNG files are allowed");
        }

        if ($file['size'] > 2000000) {
            throw new Exception("File size must be 2MB or less");
        }

        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            throw new Exception("Error uploading file");
        }

        return $targetFile;
    }

    private function insertEmployee($name, $email, $password, $phone, $qualification, $role, $photoPath) {
        $stmt = $this->conn->prepare("
            INSERT INTO users 
            (name, email, password, phone, qualification, role, photo) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $name, $email, $password, $phone, $qualification, $role, $photoPath);

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
    }

    private function sendWelcomeEmail($email, $name, $plainPassword) {
        try {
            $this->mailer->setFrom('maulikkikani.nitsan@gmail.com', 'NITSAN');
            $this->mailer->addAddress($email, $name);
            $this->mailer->Subject = 'Welcome to Our Company!';
            $this->mailer->isHTML(true);
            $this->mailer->Body = "
                <h3>Dear $name,</h3>
                <p>Congratulations! Your account has been created.</p>
                <p><b>Email:</b> $email</p>
                <p><b>Password:</b> $plainPassword</p>
                <p>You can now log in to your account.</p>
                <br>
                <p>Best Regards,</p>
                <p>NITSAN</p>
            ";

            if (!$this->mailer->send()) {
                throw new Exception("Email could not be sent. Error: {$this->mailer->ErrorInfo}");
            }
        } catch (Exception $e) {
            throw new Exception("Mailer Error: " . $e->getMessage());
        }
    }
}
?>