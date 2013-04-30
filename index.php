<?php
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  require_once('apis/mail/imap-client.php');
  if (isset($_GET['action'])) { $action=$_GET['action']; }
  else { $action = 'search'; }
  switch ($action) {
    case 'search':
      if (isset($_GET['mail'])) $mail=$_GET['mail'];
      else $mail='new';
      $curr=$action . '_' . $mail;
      break;
    case 'list':
      if (isset($_GET['mail'])) $mail=$_GET['mail'];
      else $mail='new';
      $curr=$action . '_' . $mail;
      break;
  }
  if (isset($_GET['prev'])) {
    $prev=$_GET['prev'];
  } else {
    $prev=NULL;
  }
  $imap = new IMAPClient();
  $imap->open();
  if ($action == 'search') {
    $mails = NULL;
    switch($mail) {
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
      $mseq="";
      foreach ($mails as $uid) { $mseq = $mseq . $uid . ","; }
      $mails_ow = $imap->fetch_seq_overview(substr($mseq, 0, -1));
      for ($c=0;$c < count($mails_ow); $c++) {
        $ci = $c + 1;
        $from = htmlentities($mails_ow[$c]->from);
        $subj = htmlentities($mails_ow[$c]->subject);
        $message = $message . "Correo número " . $ci . " de: " . $from;
        $message = $message . ", con asunto: " . $subj . "\n";
        $message = $message . "<break time=\"1000\"/>\n";
      }
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
    <field id="action">
<grammar xmlns="http://www.w3.org/2001/06/grammar"
         xml:lang="es-es" root="ROOT" mode="voice">
  <rule id="ROOT" scope="public">
    <one-of>
      <item> mostrar </item>
      <item> muéstrame </item>
      <item> buscar </item>
      <item> búscame </item>
    </one-of>
    <one-of>
<?php
if ($NUM_MAILS > 0) {
  $uids="?";
  foreach ($mails as $uid) {
    $uids = $uids . "uid[]=" . $uid . "&amp;"; 
  }
  $uids=$uids . "dummy=";
  echo "
      <item>
        <ruleref uri=\"./gram/show_msg.php" . $uids . "\"/>
        <tag>out.SEARCH=\"msg\"; out.UID=rules.SHOW_MSG.UID;</tag>
      </item>
";
}
?>
      <item>
        <ruleref uri="#SEARCH_NEW_MAIL"/>
        <tag>out.SEARCH="new"</tag>
      </item>
      <item>
        <ruleref uri="#SEARCH_ALL_MAIL"/>
        <tag>out.SEARCH="all"</tag>
      </item>
      <item>
        <ruleref uri="#SEARCH_DAY_MAIL"/>
        <tag>
          out.SEARCH="day";
          out.DATE=rules.SEARCH_DAY_MAIL.DATE;
        </tag>
      </item>
    </one-of>
  </rule>

  <rule id="SEARCH_NEW_MAIL">
    <item repeat="0-1"> los </item>
    <item repeat="0-1">
      <one-of>
        <item> correos </item>
        <item> mensajes </item>
      </one-of>
    </item>
    <one-of>
      <item> nuevos </item>
      <item> "no leídos" </item>
      <item> "sin leer" </item>
    </one-of>
  </rule>

  <rule id="SEARCH_ALL_MAIL">
    <one-of>
      <item> "todos los correos" </item>
      <item> "todos" </item>
      <item> "todo" </item>
      <item> "todo el correo" </item>
    </one-of>
  </rule>

  <rule id="SEARCH_DAY_MAIL">
    <one-of>
      <item> "el correo" </item>
      <item> "los correos" </item>
    </one-of>
    <one-of>
      <item> "de ayer" <tag>out.DATE="yesterday"</tag></item>
      <item> "de hoy" <tag>out.DATE="today"</tag></item>
      <item>
	<item>"del día"</item>
        <ruleref uri="./gram/date.php#DATE"/>
      </item>
    </one-of>
  </rule>
</grammar>
      <filled>
        <if cond="lastresult$.interpretation.SEARCH == 'new'">
          <prompt>Quieres ver los nuevos mensajes.</prompt>
          <goto next="./index.php?action=search&amp;mail=new" />
        </if>
        <if cond="lastresult$.interpretation.SEARCH == 'all'">
          <prompt>Quieres ver todos los mensajes.</prompt>
          <goto next="./index.php?action=search&amp;mail=all" />
        </if>
        <if cond="lastresult$.interpretation.SEARCH == 'day'">
          <prompt>
            Quieres ver los mensajes del día
            <value expr="lastresult$.interpretation.DATE" />
          </prompt>
	  <goto expr="'./index.php?action=search&amp;mail=day&amp;date=' + lastresult$.interpretation.DATE" />
        </if>
	<if cond="lastresult$.interpretation.SEARCH == 'msg'">
          <prompt>
            Quieres leer el mensaje
            <value expr="lastresult$.interpretation.UID"/>
          </prompt>
        </if>
      </filled>
    </field>
  </form>
</vxml>
