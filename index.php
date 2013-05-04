<?php
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  require_once('apis/mail/imap-client.php');
  if (isset($_GET['search'])) { $search=$_GET['search']; }
  else { $search = 'new'; }
  if (isset($_GET['prev'])) {
    $prev=$_GET['prev'];
  } else {
    $prev=NULL;
  }
  $imap = new IMAPClient();
  $imap->open();
  $mails = NULL;
  switch($search) {
    case 'new':
      $mails = $imap->search_unseen_mails();
      if (!$mails) {
        $message = "No tienes correos nuevos.\n";
      } else {
        $message = "Tienes " . count($mails) . " correos nuevos.\n";
      }
      break;
    case 'all':
      $mails = $imap->search_all_mails();
      if (!$mails) {
        $message = "No tienes ningún correo.\n";
      } else {
        $message = "Tienes un total de " . count($mails) . " correos en tu bandeja de entrada.\n";
      }
      break;
    case 'day':
      $date=$_GET['date'];
      switch ($date) {
        case 'today':
          $date=date('d-M-Y', time());
          break;
        case 'yesterday':
          $date=date('d-M-Y', time() - 86400);
          break;
        default:
          break;
      }
      $mails = $imap->search_date_mails($date);
      if (!$mails) {
        $message = "No tienes correos del día " . $date . ".\n";
      } else {
        $message = "Tienes " . count($mails) . " correos del dia " . $date . ".\n";
      }
      break;
  }
  $NUM_MAILS = 0;
  if ($mails) {
    $NUM_MAILS = count($mails);
    // Complete Main_Actions grammar URI
    $SHOW_MSG_URI = './gram/show_msg.php?';
    for($i=0;$i<$NUM_MAILS - 1;$i++) {
      $uid = $mails[$i];
      $SHOW_MSG_URI .= 'uid[]=' . $uid . '&amp;';
    }
    $SHOW_MSG_URI .= 'uid[]=' . $mails[$NUM_MAILS - 1];
    // Get Mail information
    $mseq="";
    foreach ($mails as $uid) { $mseq = $mseq . $uid . ","; }
    $mails_ow = $imap->fetch_seq_overview(substr($mseq, 0, -1));
    for ($c=0;$c < count($mails_ow); $c++) {
      $ci = $c + 1;
      $from = htmlentities($mails_ow[$c]->from);
      $subj = "";
      $elems = imap_mime_header_decode($mails_ow[$c]->subject);
      foreach($elems as $el) {
        if (strtolower($el->charset) == 'default') {
          $subj = $subj . $el->text;
        } else {
          $subj = $subj . iconv($el->charset, 'UTF-8', $el->text);
        }
      }
      $message .= "Correo número " . $ci . " de: " . $from;
      $message .= ", con asunto: " . $subj . "\n";
      $message .= "<break time=\"700\"/>\n";
    }
  }
  $imap->close();
?>
<vxml version="2.1" xmlns="http://www.w3.org/2001/vxml" xml:lang="es-es">
  <meta name="maintainer" content="joapuipe@upv.es" />
  <form id="MainForm">
    <block>
      <prompt>
        <?php
          if (is_null($prev)) { echo "Bienvenido!\n"; }
          echo $message;
        ?>
      </prompt>
    </block>
    <field name="action">
<grammar xmlns="http://www.w3.org/2001/06/grammar"
         xml:lang="es-es" root="ROOT" mode="voice">
  <rule id="ROOT" scope="public">
    <one-of>
      <item><ruleref uri="./gram/basic_actions.grxml#ROOT"/></item>
      <item><ruleref uri="./gram/main_actions.grxml#ROOT"/></item>
<?php
  if($NUM_MAILS > 0) {
    echo "
      <item><ruleref uri=\"$SHOW_MSG_URI\"/></item>
";
  }
?>
    </one-of>
  </rule>
</grammar>
      <filled>
        <if cond="lastresult$.interpretation.ACTION == 'stop'">
          <clear namelist="action"/>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'exit'">
          <prompt>Hasta pronto!</prompt>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'search'">
          <if cond="lastresult$.interpretation.SEARCH == 'new'">
            <goto next="./index.php?prev=index&amp;search=new" />
          </if>
          <if cond="lastresult$.interpretation.SEARCH == 'all'">
            <goto next="./index.php?prev=index&amp;search=all" />
          </if>
          <if cond="lastresult$.interpretation.SEARCH == 'day'">
            <goto expr="'./index.php?prev=index&amp;search=day&amp;date=' + lastresult$.interpretation.DATE" />
          </if>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'read'">
          <goto expr="'./read.php?prev=index&amp;uid=' + lastresult$.interpretation.UID" />
        </if>
      </filled>
    </field>
  </form>
</vxml>
