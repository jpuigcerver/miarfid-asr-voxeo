<?php
class IMAPClient {
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

  public function search_all_mails() {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'ALL', SE_UID);
  }

  public function search_unseen_mails() {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'UNSEEN', SE_UID);
  }

  public function search_before_mails($date) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'BEFORE \"$date\"', SE_UID);
  }

  public function last_error() {
    return imap_last_error();
  }
}
?>