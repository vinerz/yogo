<?php
/**
  *  @name              Mail
  *  @description       Send e-mails. See mail.config.php for details.
  *  @author            Vinicius Tavares <vinerz@vinerz.net>
  *  @plugin_url        http://www.xenon-corporation.com/yogo/plugin/mail
  *  @plugin_sysname    mail
  *  @plugin_version    1.0.0
  */

define("MAIL_BASEPATH",     dirname(__FILE__));
define("MAIL_TEMPLATE_DIR", MAIL_BASEPATH . "/templates");

require_once(MAIL_BASEPATH . "/mail.config.php");
require_once(MAIL_BASEPATH . "/phpmailer/class.phpmailer.php");
  
class Mail extends Core {
	private function __construct() {}
	
	public static function send($title, $message, $to, $name = false, $extramails = false) {
		$mail = new PHPMailer();
        
		if(MAIL_USE_SMTP) {
            $mail->IsSMTP();
            $mail->SMTPAuth   = MAIL_SMTP_AUTH;    
            $mail->SMTPSecure = MAIL_SMTP_SECURE;
            $mail->Host       = MAIL_SMTP_HOST;
            $mail->Port       = MAIL_SMTP_PORT;
            $mail->Username   = MAIL_SMTP_USER;
            $mail->Password   = MAIL_SMTP_PASS;
		}
        
		$mail->AddReplyTo(MAIL_REPLYTO_EMAIL, MAIL_REPLYTO_NAME);
		
		if(is_array($to)) {
			if(!@is_array($to[0])) {
				$mail->AddAddress($to[0], $to[1]);
			}
		} else {
			if(!$name) $name = $to;
			$mail->AddAddress($to, $name);
		}
		
		$mail->SetFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        
        if(MAIL_IS_HTML) $mail->IsHTML(true);
        
		$mail->Subject = utf8_decode($title);
        
		$mail->AltBody = MAIL_ALT_BODY;
		$mail->Body = $message;
		
        $result = $mail->Send();
        
        $mail->ClearAllRecipients();
        $mail->ClearAttachments();

		if($result) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function format($template, $strings) {
        $filename = MAIL_TEMPLATE_DIR . "/" . $template . ".tpl";
		if(is_file($filename)) {
			$buffer = file_get_contents($filename);
			foreach($strings as $key => $val) {
				$buffer = str_replace("{".strtoupper($key)."}", $val, $buffer);
			}
			return $buffer;
		} else {
            self::log("Requested template '".$template."' for mail doesn't exist.", YG_WARNING);
			return false;
		}
	}
}