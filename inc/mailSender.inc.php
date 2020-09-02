<?
class MailSender
{
	function send($fromAddress, $fromName, $toAddress, $toName, $subject, $contents)
	{
		$toName = iconv("utf-8", "euc-kr", $toName);
		$fromName = iconv("utf-8", "euc-kr", $fromName);
		$subject = iconv("utf-8", "euc-kr", $subject);
		$contents = iconv("utf-8", "euc-kr", $contents);


		$mailheaders .= "Return-Path: <". $fromAddress. ">\n";
		$mailheaders .= "From: ". $fromName. " <". $fromAddress. ">\n";
		$mailheaders .= "X-Sender: <". $fromAddress. ">\n";
		$mailheaders .= "X-Mailer: PHP\n";
		$mailheaders .= "Reply-To: ". $fromName. " <". $fromAddress. ">\n";
		$mailheaders .= "MIME-Version: 1.0\n";
		$mailheaders .= "Content-Type: text/html;\n\tcharset=euc-kr";


		$to = $toName. " <". $toAddress. ">";

		mail($to, $subject, $contents, $mailheaders);
	}
}