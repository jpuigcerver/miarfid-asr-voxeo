<?php
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  require_once('apis/mail/imap-client.php');
  $imap = new IMAPClient();
  $imap->open();
  $num_mails = count($imap->search_all_mails());
  $imap->close();
?>
<vxml version="2.1" xmlns="http://www.w3.org/2001/vxml">
  <meta name="maintainer" content="joapuipe@upv.es" />
  <form id="Welcome">
    <block>
      <prompt>Hello! You have <?php echo "$num_mails"; ?> new messages.</prompt>
    </block>
  </form>
</vxml>