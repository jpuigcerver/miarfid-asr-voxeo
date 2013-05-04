<?php
ini_set('display_errors','On');
error_reporting(E_ALL);

class IMAPClient {
  private $imap = FALSE;
  private $host = '{imap.gmail.com:993/imap/ssl}INBOX';
  private $user = 'emilia.rah@gmail.com';
  private $pass = 'contrasenyadeemilia';
  /*private $host = '{imap.puigcerver.me:143/imap/tls/novalidate-cert}INBOX';
  private $user = 'emilia.rah@puigcerver.me';
  private $pass = 'ux#rjm*3';*/
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

  private function getpart($uid, $p, $pno) {
    $result = array();
    $result['text'] = '';
    $result['attach'] = FALSE;

    // DECODE DATA
    $data = ($pno) ?
        imap_fetchbody($this->imap, $uid, $pno, FT_UID) :  // multipart
        imap_body($this->imap, $uid, FT_UID);  // simple
    if ($p->encoding == 4) {
      $data = quoted_printable_decode($data);
    } else if ($p->encoding == 3) {
      $data = base64_decode($data);
    }

    // PARAMETERS
    $params = array();
    if (isset($p->parameters)) {
        foreach ($p->parameters as $x)
            $params[strtolower($x->attribute)] = $x->value;
    }
    if (isset($p->dparameters)) {
        foreach ($p->dparameters as $x)
            $params[strtolower($x->attribute)] = $x->value;
    }

    // ATTACHMENT
    if (isset($params['filename']) || isset($params['name'])) {
      $result['attach'] = TRUE;
    }

    // TEXT
    if ($p->type == 0 && $data) {
      if (strtolower($p->subtype) == 'plain') {
        $result['text'] = iconv($params['charset'], 'UTF-8', trim($data));
      }
    }

    // SUBPART RECURSION
    if (isset($p->parts)) {
      foreach ($p->parts as $qno => $q) {
        $part_res = $this->getpart($uid, $q, $pno . '.' . ($qno+1));
        $result['text'] .= trim("\n\n" . $part_res['text']);
        $result['attach'] = $result['attach'] || $part_res['attach'];
      }
    }
    return $result;
  }

  public function fetch_body($uid) {
    if (!$this->imap and !$this->open()) return FALSE;
    $s = imap_fetchstructure($this->imap, $uid, FT_UID);
    if (!isset($s->parts)) {
      $result = $this->getpart($uid, $s, 0);
    } else {
      $result = array();
      $result['text'] = '';
      $result['attach'] = FALSE;
      foreach ($s->parts as $pno => $p) {
        $part_res = $this->getpart($uid, $p, $pno+1);
        $result['text'] .= trim("\n\n" . $part_res['text']);
        $result['attach'] = ($result['attach'] || $part_res['attach']);
      }
    }
    return $result;
  }
  public static function header_decode($head) {
    $elems = imap_mime_header_decode($head);
    $dec = '';
    foreach($elems as $el) {
      if (strtolower($el->charset) == 'default') {
        $dec .= $el->text;
      } else {
        $dec .= iconv($el->charset, 'UTF-8', $el->text);
      }
    }
    return $dec;
  }
}
?>