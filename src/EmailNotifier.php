<?php

namespace EmailNotifier;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class EmailNotifier
{
  private $mailer;
  private $from;
  private $to;

  public function __construct(string $from, $to)
  {
    $config = new Config();
    $dsn = "smtp://{$config->email}:{$config->emailPass}@{$config->stmp}";

    $this->from = $from;
    $this->to = is_array($to) ? $to : [$to];

    $transport = Transport::fromDsn($dsn);
    $this->mailer = new Mailer($transport);
  }

  public function sendSuccess(string $subject, string $message, bool $isHtml = false): void
  {
    $this->sendEmail("[SUCESSO] " . $subject, $message, $isHtml);
  }

  public function sendError(string $subject, string $message, bool $isHtml = false): void
  {
    $this->sendEmail("[ERRO] " . $subject, $message, $isHtml);
  }

  private function sendEmail(string $subject, string $message, bool $isHtml = false): void
  {
    $email = (new Email())
      ->from($this->from)
      ->to(...$this->to)
      ->subject($subject);

    if ($isHtml) {
      $email->html($message);
    } else {
      $email->text($message);
    }

    $this->mailer->send($email);
  }
}