<?php
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  require_once('apis/mail/imap-client.php');
  if (!isset($_GET['uid'])) {
    trigger_error("Expected message \"uid\".", E_USER_ERROR);
  }
  $UID=$_GET['uid'];
  $imap = new IMAPClient();
  $imap->open();
  $ovw = $imap->fetch_overview($UID);
  $from = htmlspecialchars($ovw->fromaddress, ENT_COMPAT, 'UTF-8');
  $subj = IMAPClient::header_decode($ovw->subject);
  $body = $imap->fetch_body($UID);
  $text = htmlspecialchars($body['text'], ENT_COMPAT, 'UTF-8');
  $attach = $body['attach'];
  $message = "Correu electrònic de: $from.\n";
  $message .= "<break time=\"500\"/>\n";
  $message .= "Assumpte: \"$subj\".\n";
  $message .= "<break time=\"700\"/>\n";
  $message .= "$text\n";
  if ($attach) {
    $message .= "<break time=\"700\"/>\n";
    $message .= "Aquest correu inclou fitxers adjunts que no poc mostrar.\n";
  }
  $UNSEEN=0;
  if (isset($_GET['unseen']) && $_GET['unseen'] == 1) {
    $imap->mark_unseen($UID);
    $UNSEEN=1;
  }
  $imap->close();
?>
<vxml version="2.1" xmlns="http://www.w3.org/2001/vxml" xml:lang="ca-es">
  <meta name="maintainer" content="joapuipe@upv.es" />
  <form id="MainForm">
<?php
if ($UNSEEN == 0) {
  echo "
    <block>
      <prompt>" . $message . "</prompt>
    </block>
";
}
?>
    <field name="action">
      <grammar xmlns="http://www.w3.org/2001/06/grammar"
               xml:lang="ca-es" root="ROOT" mode="voice">
        <rule id="ROOT" scope="public">
          <one-of>
            <item><ruleref uri="./gram/basic_actions.grxml#ROOT"/></item>
            <item><ruleref uri="./gram/main_actions.grxml#ROOT"/></item>
            <item><ruleref uri="./gram/read_actions.grxml#ROOT"/></item>
          </one-of>
        </rule>
      </grammar>
      <filled>
        <if cond="lastresult$.interpretation.ACTION == 'stop'">
          <clear namelist="action"/>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'exit'">
          <prompt>Fins prompte!</prompt>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'delete'">
          <goto next="./delete.php?prev=read&amp;uid=<?php echo $UID; ?>"/>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'mark_unseen'">
          <goto next="./read.php?prev=read&amp;unseen=1&amp;uid=<?php echo $UID; ?>"/>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'read_again'">
          <goto next="./read.php?prev=read&amp;uid=<?php echo $UID; ?>"/>
        </if>
        <if cond="lastresult$.interpretation.ACTION == 'search'">
          <if cond="lastresult$.interpretation.SEARCH == 'new'">
            <goto next="./index.php?prev=read&amp;search=new"/>
          </if>
          <if cond="lastresult$.interpretation.SEARCH == 'all'">
            <goto next="./index.php?prev=read&amp;search=all"/>
          </if>
          <if cond="lastresult$.interpretation.SEARCH == 'day'">
            <goto expr="'./index.php?prev=read&amp;search=day&amp;date=' + lastresult$.interpretation.DATE"/>
          </if>
        </if>
      </filled>
    </field>
  </form>
</vxml>