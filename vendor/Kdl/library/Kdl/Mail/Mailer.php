<?php

namespace Kdl\Mail;

use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail\Message;
use Zend\ServiceManager\ServiceLocatorInterface;
use Exception;

class Mailer
{

    /**
     * @var ServiceLocatorInterface
     */
    protected $svLocator;

    /**
     * @var \Application\Application\Service\SystemLog
     */
    protected $systemLogSv;

    public function __construct(ServiceLocatorInterface $svLocator)
    {
        $this->svLocator = $svLocator;
    }

    /**
     * Get system log service
     * @return \Application\Application\Service\SystemLog
     */
    protected function getSystemLogSv()
    {
        if (!isset($this->systemLogSv)) {
            $this->systemLogSv = $this->svLocator->get('SystemLogService');
        }
        return $this->systemLogSv;
    }

    /**
     * @return array
     */
    protected function getSmtpOptions()
    {
        $configs = $this->svLocator->get('config');
        $smtpOptions = isset($configs['smtp_options']) ? $configs['smtp_options'] : array();

        return $smtpOptions;
    }

    /**
     * Get mail address of sender from smtp_options config
     * @return string
     */
    public function getSenderMail()
    {
        $options = new SmtpOptions($this->getSmtpOptions());
        $connectionConfig = $options->getConnectionConfig();
        $senderMail = isset($connectionConfig['from']) ? $connectionConfig['from'] : '';

        return $senderMail;
    }

    /**
     * Send email
     * @param type $subject
     * @param type $body
     * @param array $toAddresses
     * @param string|array $bcc default: <b>null</b>
     * @return boolean
     */
    public function sendMail($subject, $body, array $toAddresses, $bcc = null)
    {
        $options = new SmtpOptions($this->getSmtpOptions());
        $connectionConfig = $options->getConnectionConfig();
        $senderName = isset($connectionConfig['name']) ? $connectionConfig['name'] : '';
        //--------- instance mail
        $mail = new Message();
        $mail->setFrom($this->getSenderMail(), $senderName);
        $mail->setBody($body);
        $mail->setTo($toAddresses);
        $mail->setSubject($subject);
        $mail->setEncoding('UTF-8');

        if ($bcc != null) {
            $mail->setBcc($bcc);
        }

        try {
            $transport = new Smtp($options);
            $transport->send($mail);
            return true;
        } catch (Exception $exc) {
            $this->getSystemLogSv()->err($exc->getMessage(), $exc->getTrace());
            return false;
        }
    }

    /**
     *
     * @param type $file_path
     * @return type
     */
    public function getBodyFromTemplate($file_path)
    {
        $body = '';
        if (is_file($file_path)) {
            $body = file_get_contents($file_path);
        }  else {
            $this->getSystemLogSv()->err($file_path.'が見つかりません');
            return false;
        }
        return $body;
    }
}
