<?php
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  require_once('apis/mail/imap-client.php');
  if (!isset($_GET['uid'])) {
    trigger_error("Expected message \"uid\".", E_USER_ERROR);
  }
  $UID=$_GET['uid'];
  $imap = new IMAPClient();
  $imap->open();
  $imap->delete($UID);
  $imap->close();
?>
<vxml version="2.1" xmlns="http://www.w3.org/2001/vxml" xml:lang="ca-es">
  <meta name="maintainer" content="joapuipe@upv.es" />
  <form id="MainForm">
    <block><prompt> Correu esborrat. </prompt></block>
    <field name="action">
      <grammar xmlns="http://www.w3.org/2001/06/grammar"
               xml:lang="ca-es" root="ROOT" mode="voice">
        <rule id="ROOT" scope="public">
          <one-of>
            <item><ruleref uri="./gram/basic_actions.grxml#ROOT"/></item>
            <item><ruleref uri="./gram/main_actions.grxml#ROOT"/></item>
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
        <if cond="lastresult$.interpretation.ACTION == 'search'">
          <if cond="lastresult$.interpretation.SEARCH == 'new'">
            <goto next="./index.php?prev=delete&amp;search=new"/>
          </if>
          <if cond="lastresult$.interpretation.SEARCH == 'all'">
            <goto next="./index.php?prev=delete&amp;search=all"/>
          </if>
          <if cond="lastresult$.interpretation.SEARCH == 'day'">
            <goto expr="'./index.php?prev=delete&amp;search=day&amp;date=' + lastresult$.interpretation.DATE"/>
          </if>
        </if>
      </filled>
    </field>
  </form>
</vxml>
