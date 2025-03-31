<?php
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Model class for handling employee registration including database operations,
 * photo uploads, and sending welcome emails.
 */
class RegisterEmployeeModel {
    /** @var mysqli Database connection object */
    private $conn;
    
    /** @var PHPMailer Email service object */
    private $mailer;

    /**
     * Constructor - initializes the database connection and mailer
     * @param mysqli $connection Database connection object
     * @throws Exception If mailer initialization fails
     */
    public function __construct(mysqli $connection) {
        $this->conn = $connection;
        $this->initializeMailer();
    }

    /**
     * Initializes PHPMailer with SMTP configuration
     * @return void
     * @throws Exception If mailer configuration fails
     */
    private function initializeMailer(): void {
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

    /**
     * Registers a new employee with photo upload and welcome email
     * @param array $data Employee data including:
     *               - name: string
     *               - email: string
     *               - password: string
     *               - phone: string
     *               - qualification: string
     *               - role: string
     * @param array|null $file Uploaded file data (from $_FILES)
     * @return void
     * @throws Exception If any step in the registration process fails
     */
    public function registerEmployee(array $data, ?array $file = null): void {
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

    /**
     * Handles photo upload and validation
     * @param array|null $file Uploaded file data
     * @return string|null Path to uploaded photo or null if no file
     * @throws Exception If file validation or upload fails
     */
    private function handlePhotoUpload(?array $file): ?string {
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

    /**
     * Inserts employee data into the database
     * @param string $name Employee name
     * @param string $email Employee email
     * @param string $password Hashed password
     * @param string $phone Phone number
     * @param string $qualification Employee qualification
     * @param string $role Employee role
     * @param string|null $photoPath Path to employee photo
     * @return void
     * @throws Exception If database operation fails
     */
    private function insertEmployee(
        string $name,
        string $email,
        string $password,
        string $phone,
        string $qualification,
        string $role,
        ?string $photoPath
    ): void {
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

    /**
     * Sends welcome email with login credentials
     * @param string $email Recipient email address
     * @param string $name Recipient name
     * @param string $plainPassword Plain text password (for initial login)
     * @return void
     * @throws Exception If email sending fails
     */
    private function sendWelcomeEmail(string $email, string $name, string $plainPassword): void {
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