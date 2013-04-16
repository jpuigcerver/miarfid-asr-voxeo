<?php
class GmailClient {
  private $imap = FALSE;
  private $host = '{imap.gmail.com:993/imap/ssl}INBOX';
  private $user = 'emilia.rah@gmail.com';
  private $pass = 'contrasenyadeemilia';
  public function open() {
    $this->imap = imap_open($this->host, $this->user, $this->pass);
    return $this->imap;
  }

  public function close() {
    return imap_close($this->imap);
  }

  public function fetch_new_mails() {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'NEW');
  }

  public function last_error() {
    return imap_last_error();
  }
}
?>