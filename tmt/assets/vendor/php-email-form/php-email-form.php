<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

class PHP_Email_Form {

  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $smtp = null;
  public $ajax = false;
  private $messages = [];

  public function add_message($content, $label, $priority = 0) {
    $this->messages[] = [
      'content' => $content,
      'label' => $label,
      'priority' => $priority
    ];
  }

  public function send() {
    // Kiểm tra nếu không có địa chỉ email nhận
    if (empty($this->to)) {
      return 'Receiving email address is missing!';
    }

    // Tạo nội dung email
    $email_content = '';
    foreach ($this->messages as $message) {
      $email_content .= $message['label'] . ": " . $message['content'] . "\n";
    }

    // Headers của email
    $headers = "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
    $headers .= "Reply-To: " . $this->from_email . "\r\n";
    
    // Nếu có cấu hình SMTP thì gửi qua SMTP
    if (!empty($this->smtp)) {
      return $this->send_smtp_email($email_content, $headers);
    }

    // Gửi email qua hàm mail() mặc định của PHP
    if (mail($this->to, $this->subject, $email_content, $headers)) {
      return 'Email sent successfully!';
    } else {
      return 'Failed to send email.';
    }
  }

  private function send_smtp_email($email_content, $headers) {
    // Cấu hình chi tiết SMTP (nếu bạn sử dụng thư viện PHPMailer, đây là nơi bạn sẽ xử lý nó)
    // Giả định rằng thư viện PHPMailer được cài đặt qua composer hoặc bằng cách khác
    
    // Nếu bạn sử dụng PHPMailer, bạn cần thêm mã xử lý gửi mail qua SMTP ở đây.
    // Đoạn code bên dưới là một ví dụ:

    $mail = new PHPMailer(true); // Sử dụng PHPMailer

    try {
      // Cấu hình SMTP
      $mail->isSMTP();
      $mail->Host = $this->smtp['host'];
      $mail->SMTPAuth = true;
      $mail->Username = $this->smtp['username'];
      $mail->Password = $this->smtp['password'];
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = $this->smtp['port'];

      // Thiết lập thông tin người gửi
      $mail->setFrom($this->from_email, $this->from_name);
      $mail->addAddress($this->to);

      // Nội dung email
      $mail->isHTML(false); // Không dùng HTML
      $mail->Subject = $this->subject;
      $mail->Body = $email_content;

      // Gửi email
      $mail->send();
      return 'Email sent successfully using SMTP!';
    } catch (Exception $e) {
      return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}
?>
