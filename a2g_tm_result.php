<?php
global $wpdb;
ob_start();
$current_file = get_option( 'a2g_tm_output_css' );

echo "<html>
<head>
<style>
".$current_file."
</style>
</head>
<body class=\"a2g-out-body\">
<div class=\"a2g-out-html\">";
foreach ($_POST as $key => $value) {
    $a2g_catid_textid = explode(",",$value);
    echo html_entity_decode ( a2g_tm_get_text_by_textid($a2g_catid_textid[1]) );
    echo "<br><br>";
}
echo "</div>
</body>
</html>";

$a2g_tm_seiteninhalt = ob_get_contents();

ob_end_flush();

$a2g_tm_dir = ABSPATH . 'wp-content/plugins/ada2go-text-modules/reslut-safes/';

$a2g_tm_filecount = 0;

$a2g_tm_files = glob($a2g_tm_dir . "*");

    if ($a2g_tm_files){
    
     $a2g_tm_filecount = count($a2g_tm_files);
     
    }

$a2g_tm_name_time = time();

$a2g_tm_file = fopen($a2g_tm_dir.$a2g_tm_filecount.'_'.$a2g_tm_name_time.'_safe.html', "w");

fputs($a2g_tm_file, $a2g_tm_seiteninhalt);

fclose($a2g_tm_file);