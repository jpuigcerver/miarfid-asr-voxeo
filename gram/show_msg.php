<?php
  ini_set('display_errors','On');
  error_reporting(E_ALL);
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<grammar xmlns="http://www.w3.org/2001/06/grammar"
         xml:lang="ca-es" root="ROOT" mode="voice">
  <rule id="ROOT" scope="public">
    <one-of>
      <item> "mostrar" </item>
      <item> "mostra'm" </item>
      <item> "llegir" </item>
      <item> "llegeix" </item>
      <item> "llegeix-me" </item>
    </one-of>
    <item repeat="0-1"> el </item>
    <one-of>
      <item> missatge </item>
      <item> correu </item>
    </one-of>
    <item repeat="0-1"> número </item>
    <one-of>
<?php
$num_mails = count($_GET['uid']);
for ($i=1;$i<=$num_mails;$i++) {
$uid=$_GET['uid'][$i-1];
echo "<item> $i <tag>out.UID=$uid</tag></item>\n";
}
?>
    </one-of>
    <tag>out.ACTION="read";</tag>
  </rule>
</grammar>
