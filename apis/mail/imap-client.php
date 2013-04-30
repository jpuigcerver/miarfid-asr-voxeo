<?php

ini_set('display_errors','On');
error_reporting(E_ALL);

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

  public function search_from_mails($from) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'FROM "' . $from . '"', SE_UID);
  }

  public function search_to_mails($to) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'TO "' . $to . '"', SE_UID);
  }

  public function search_before_mails($date) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'BEFORE "' . $date . '"', SE_UID);
  }

  public function search_since_mails($date) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'SINCE "' . $date . '"', SE_UID);
  }

  public function search_date_mails($date) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_search($this->imap, 'ALL ON "' . $date . '"', SE_UID);
  }

  public function last_error() {
    return imap_last_error();
  }

  public function fetch_overview($uid) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_headerinfo($this->imap, $uid, FT_UID);
  }

  public function fetch_seq_overview($seq) {
    if (!$this->imap and !$this->open()) return FALSE;
    return imap_fetch_overview($this->imap, $seq, FT_UID);
  }
}
?>