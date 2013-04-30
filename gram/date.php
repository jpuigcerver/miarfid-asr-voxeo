<?php
  ini_set('display_errors','On');
  error_reporting(E_ALL);
  $CURR_MONTH=date('M');
  $CURR_YEAR=date('Y');
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<grammar xmlns="http://www.w3.org/2001/06/grammar"
         xml:lang="es-es" root="DATE" mode="voice">
  <rule id="DATE" scope="public">
    <item>
      <ruleref uri="#DAY"/>
      <ruleref uri="#MONTH"/>
      <ruleref uri="#YEAR"/>
      <tag>
        out.DATE=rules.DAY + "-" + rules.MONTH + "-" + rules.YEAR;
      </tag>
    </item>
  </rule>
  <rule id="DAY">
    <one-of>
<?php
for($i=1;$i<=31;$i++) {
  echo "<item> $i <tag>out=\"$i\"</tag> </item>\n";
}
?>
    </one-of>
  </rule>
  <rule id="MONTH">
    <one-of>
      <item>
        <item repeat="0-1"> de </item>
        <one-of>
          <item> enero <tag>out="Jan"</tag></item>
          <item> febrero <tag>out="Feb"</tag></item>
          <item> marzo <tag>out="Mar"</tag></item>
          <item> abril <tag>out="Apr"</tag></item>
          <item> mayo <tag>out="May"</tag></item>
          <item> junio <tag>out="Jun"</tag></item>
          <item> julio <tag>out="Jul"</tag></item>
          <item> agosto <tag>out="Aug"</tag></item>
          <item> septiembre <tag>out="Sep"</tag></item>
          <item> octubre <tag>out="Oct"</tag></item>
          <item> noviembre <tag>out="Nov"</tag></item>
          <item> diciembre <tag>out="Dec"</tag></item>
        </one-of>
      </item>
      <item><tag>out="<?php echo $CURR_MONTH; ?>"</tag></item>
    </one-of>
  </rule>
  <rule id="YEAR">
    <one-of>
      <item>
        <item repeat="0-1"> de </item>
        <one-of>
<?php
for($y=1950;$y<=2050;$y = $y+1) {
  echo "<item> $y <tag>out=\"$y\"</tag></item>\n";
}
?>
        </one-of>
      </item>
      <item><tag>out="<?php echo $CURR_YEAR; ?>"</tag></item>
    </one-of>
  </rule>
</grammar>
