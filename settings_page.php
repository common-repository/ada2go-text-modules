<?php
global $wpdb;

    // Handle EDIT Kat
    if($_GET['action']=="updatekat" AND (is_admin())) {
    #$_POST["edit-kat"];
    $a2g_new_kat_content = sanitize_text_field( htmlentities( $_POST["edit-kat"] ));
    $a2g_update_kategorie_id = sanitize_text_field( $_GET["id"]);
    
    $a2g_update_kategorie_sort = sanitize_text_field( $_POST["cat-sort"]);
    
    if(empty($_POST["cat-sort"])) { 
    $a2g_update_kategorie_sort = a2g_tm_get_catsort_by_catid( $a2g_update_kategorie_id ); 
    }
    
          $wpdb->update( 
          	$wpdb->prefix.'a2g_tm_kategorie',  
          	array( 
          		'catDesc' => $a2g_new_kat_content,
              'catSort' => $a2g_update_kategorie_sort
          	), 
          	array( 'catID' => $a2g_update_kategorie_id ), array( '%s', '%d' ), 
          	array( '%d' ) 
          );
          	$a2g_tm_message_out = __('<div class="updated">
              <p>'.__('Erledigt! Alles gespeichert!', 'ada2go-text-modules').'</p>
              <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
            </div>');
    }
    
    // Handle EDIT Text
    if($_GET['action']=="updatetext" AND (is_admin())) {
    $a2g_new_text_content = sanitize_text_field( htmlentities( $_POST["edit-text"] ));
    $a2g_update_text_id = sanitize_text_field( $_GET["id"] );
          $wpdb->update( 
          	$wpdb->prefix.'a2g_tm_text',  
          	array( 
          		'text' => $a2g_new_text_content
          	), 
          	array( 
          		'textID' => $a2g_update_text_id 
          	),
          	array( 
          		'%s'
          	), 
          	array( '%s'
          		 ) 
          );
          	$a2g_tm_message_out = __('<div class="updated">
              <p>'.__('Erledigt! Alles gespeichert!', 'ada2go-text-modules').'</p>
              <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
            </div>');
            
    }
    
    
    // Handle EDIT Output CSS
    if($_GET['action']=="editoutcss" AND (is_admin())) {
    $a2g_new_css_content = sanitize_text_field( htmlentities( $_POST["css-output"] ));
    update_option( 'a2g_tm_output_css', $a2g_new_css_content, $autoload );
          	$a2g_tm_message_out = __('<div class="updated">
              <p>'.__('Erledigt! Alles gespeichert!', 'ada2go-text-modules').'</p>
              <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
            </div>');
    }

    // Handle DEL Kat
    if($_GET['action']=="delcat" AND (is_admin())) {
        $table = $wpdb->prefix . 'a2g_tm_kategorie';
        if( $wpdb->delete( $table, array( 'catID' => $_GET['id'] ) ) === FALSE) {
              echo
              '<div class="error">
                <p>' . __( 'Das hat leider nicht geklappt!', 'ada2go-text-modules' ) . '</p>
                <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
              </div>';
          } else {
              $a2g_tm_message_out = 
              '<div class="updated">
                <p>' . __( 'Kategorie wurde erfolgreich gel&ouml;scht!', 'ada2go-text-modules' ) . '</p>
                <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
              </div>';
              }
    }
    
    // Handle DEL Document
    if($_GET['action']=="deldocument" AND (is_admin())) {
    $a2g_tm_get_document_to_del = esc_url( $_GET['path'] );
            unlink($a2g_tm_get_document_to_del);
              $a2g_tm_message_out = 
              '<div class="updated">
                <p>' . __( 'Dokument wurde erfolgreich gel&ouml;scht!', 'ada2go-text-modules' ) . '</p>
                <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
              </div>';
        }
    
    
    // Handle DEL Text
    if($_GET['action']=="deltext" AND (is_admin())) {
        $table = $wpdb->prefix . 'a2g_tm_text';
        if( $wpdb->delete( $table, array( 'textID' => $_GET['id'] ) ) === FALSE) {
              $a2g_tm_message_out =
              '<div class="error">
                <p>' . __( 'Das hat leider nicht geklappt!', 'ada2go-text-modules' ) . '</p>
                <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
              </div>';
          } else {
              $a2g_tm_message_out = 
              '<div class="updated">
                <p>' . __( 'Text wurde erfolgreich gel&ouml;scht!', 'ada2go-text-modules' ) . '</p>
                <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
              </div>';
              }
    }
    
if($_POST AND (is_admin())) {
    // Handle INSERT Cat
    if($_GET['action']=="addcat") {
          $table = $wpdb->prefix . 'a2g_tm_kategorie';
          $a2g_filter_input_catdesc = stripslashes( $_POST['kat-desc'] );
          $a2g_filter_input_catname = sanitize_text_field( $_POST['cat-name'] );
          $a2g_filter_input_catsort = a2g_tm_get_highest_cat_sort()+1;
          $wpdb->insert($table, 
          array('catID' => '', 
          'catName' => $a2g_filter_input_catname,
          'catDesc' => $a2g_filter_input_catdesc,
          'catSort' => $a2g_filter_input_catsort
          )); 
          $a2g_tm_message_out = '<div class="updated">
              <p>' . __( 'Kategorie wurde erfolgreich angelegt!', 'ada2go-text-modules' ) . '</p>
              <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
            </div>';
    }
    // Handle INSERT Text
    if($_GET['action']=="addtext") {
          $table = $wpdb->prefix . 'a2g_tm_text';
          $a2g_filter_input_textcatid = sanitize_text_field( $_POST['text-kategorie'] );
          $a2g_filter_input_textname = stripslashes( $_POST['text-desc'] );
          $a2g_filter_input_textident = sanitize_text_field( $_POST['text-ident'] );
          $wpdb->insert($table, 
          array('textID' => '', 
          'catID' =>$a2g_filter_input_textcatid,
          'text' => $a2g_filter_input_textname,
          'text_ueberschrift' => $a2g_filter_input_textident                
          )); 
          $a2g_tm_message_out = '<div class="updated">
              <p>' . __( 'Text wurde erfolgreich angelegt!', 'ada2go-text-modules' ) . '</p>
              <p><a href="?page=a2g_tm_settings">'.__('Zur&uuml;ck', 'ada2go-text-modules').'</a></p>
            </div>';
    }

}

// create selectlist for categorys
	global $wpdb;
	$a2g_text_kats	=	$wpdb->get_results("SELECT *  FROM ".$wpdb->prefix."a2g_tm_kategorie");
  
  $a2g_out_kat .= '<select name="text-kategorie" id="option-kat">';
	foreach ( $a2g_text_kats as $a2g_text_kat ) {
    $a2g_out_kat .= '<option value="'.$a2g_text_kat->catID.'">'.$a2g_text_kat->catName.'</option>';
	}
  $a2g_out_kat .= '</select>';
  
  if(empty($a2g_text_kats)) {
  $a2g_out_kat = "".__( 'Noch keine Kategorien angelegt', 'ada2go-text-modules' )."";
  $a2g_tm_kat_exist = false;
  }
  
?>
<h1><?php echo esc_html_e('Text Modules - Einstellungen', 'ada2go-text-modules'); ?></h1>
<?php

if(!empty($a2g_tm_message_out)) {
echo $a2g_tm_message_out;
}

if($_GET["action"]=="editkat") {
    
    // Get the Category ID
    $a2g_tm_get_id = sanitize_text_field( $_GET["id"] );
    echo __( '<form action="?page=a2g_tm_settings&action=updatekat&id='. sanitize_text_field($a2g_tm_get_id) .'" method="post">' );
    $a2g_tm_get_editcat = a2g_tm_edit_cat_by_catid( $a2g_tm_get_id );
    echo wp_editor( html_entity_decode( $a2g_tm_get_editcat ), 'edit-kat' );
    echo __( '<br />
    Sortierung: <input type="text" name="cat-sort"><br><br>
    <input type="hidden" name="a2g-edit-id" value="'. sanitize_text_field($a2g_tm_get_id) .'">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="'. __( 'Kategorie aktualisieren', 'ada2go-text-modules' ).'"><br /><br />' );
}

if($_GET["action"]=="edittext") {

    // Get the Text ID
    $a2g_tm_get_id = sanitize_text_field( $_GET["id"] );
    echo __( '<form action="?page=a2g_tm_settings&action=updatetext&id='.sanitize_text_field($a2g_tm_get_id).'" method="post">' );
    $a2g_tm_get_edittext = a2g_tm_edit_text_by_textid($a2g_tm_get_id);
    echo wp_editor( html_entity_decode ($a2g_tm_get_edittext), 'edit-text' );
    echo __( '<br /><input type="hidden" name="a2g-edit-id" value="'.sanitize_text_field($a2g_tm_get_id) .'">
    <input type="submit" name="submit" id="submit" class="button button-primary" value="'. __( 'Text aktualisieren', 'ada2go-text-modules' ).'"><br /><br />' );
}

if(!$_GET["action"]) {
?>

<div class="tab">
  <button class="tablinks" onclick="a2gSettingTab(event, 'allgemein')"><?php echo __('Allgemeines', 'ada2go-text-modules'); ?></button>
  <button class="tablinks" onclick="a2gSettingTab(event, 'katEdit')"><?php echo __('Kategorien bearbeiten', 'ada2go-text-modules'); ?></button>
  <button class="tablinks" onclick="a2gSettingTab(event, 'katAdd')"><?php echo __('Kategorie hinzuf&uuml;gen', 'ada2go-text-modules'); ?></button>
  <button class="tablinks" onclick="a2gSettingTab(event, 'textEdit')"><?php echo __('Texte bearbeiten', 'ada2go-text-modules'); ?></button>
  <button class="tablinks" onclick="a2gSettingTab(event, 'textAdd')"><?php echo __('Texte hinzuf&uuml;gen', 'ada2go-text-modules'); ?></button>
  <button class="tablinks" onclick="a2gSettingTab(event, 'textSafes')"><?php echo __('Gespeicherte Ergebnisse', 'ada2go-text-modules'); ?></button>
</div>

<!-- Tab content -->

<div id="allgemein" class="tabcontent" style="display:block;">
  <h3>Willkommen!</h3>
<?php echo __('Mit diesem Plugin kannst du ganz einfach Text-Module erstellen und sie im Frontend, wie in einem Generator, zusammenf&uuml;gen.'); ?> 
<br><br>
<?php echo __('Als bestes Beispiel dient hier ein Generator f&uuml;r Arbeitszeugnisse: Du legst Textbausteine fest und kannst dir die passenden Bausteine "zusammenklicken" und speichern. Du kannst auf jeder beliebigen Seite (oder in Beitr&auml;ge) den Shortcode [a2gtm] einsetzen. <br><br>
Du kannst jetzt den Shortcode um die Kategorie-ID erweitern, wenn du nur eine oder mehrere Kategorien ausgeben lassen m&ouml;chtest. Dazu kannst du diesen Code verwenden: [a2gtm cat="1,2,3"] 1,2,3 sind die Kategorie-IDS die du in der Ansicht "Kategorie bearbeiten" in der Tabelle findest.
<b>Bitte trenne die Kategorien zwingend mit einem Komma, ansonsten wird das Plugin nicht funktionieren.</b>'); ?> 
<br><br>
<?php echo __('Aktuell gibt es keine Einstellungen zum einstellen. Falls du W&uuml;nsche oder Feedback f&uuml;r uns hast, melde dich gerne unter https://ada2go.de/kontakt!', 'ada2go-text-modules'); ?>
<br><br>
<h3><?php echo __('Erste Schritte', 'ada2go-text-modules'); ?></h3>
1. <?php echo __('Erstelle zuerst eine KATEGORIE (oder mehrere!)', 'ada2go-text-modules'); ?><br>
2. <?php echo __('Erstelle dann Textbausteine und ordne sie einer Kategorie zu!', 'ada2go-text-modules'); ?><br>
3. <?php echo __('Mit dem Shortcode [a2gtm] f&uuml;gst du ein Formular im FRONTEND ein das die Besucher nutzen k&ouml;nnen.', 'ada2go-text-modules'); ?><br>
4. <?php echo __('Wenn nur ausgew&auml;hlte Benutzer diese Seite aufrufen sollen, sch&uuml;tze sie mit einem Passwort oder speziellen Plugin.', 'ada2go-text-modules'); ?>
<br><br>
<h3><?php echo __('CSS der Ausgabedatei bearbeiten', 'ada2go-text-modules'); ?></h3>
<?php echo __('Verwende folgende Klassen: .a2g-out-html, .a2g-out-body ohne HTML Tags (&lt;script&gt;)', 'ada2go-text-modules'); ?>
<br>
<form action="?page=a2g_tm_settings&action=editoutcss" method="post">
<?php 
$current_file = get_option( 'a2g_tm_output_css' );
echo wp_editor( $current_file, 'css-output'); ?>
<br />
<br />
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( 'CSS f&uuml;r die Ausgabedatei speichern', 'ada2go-text-modules' ); ?>">
</p>
</form>




</div>

<div id="katEdit" class="tabcontent">
  <h3>Kategorien bearbeiten</h3>
 <div class="a2g-p-container">
<table id="a2g-table">
<tr>
<th style="width:4%;cursor:pointer;"><?php echo __( 'ID', 'ada2go-text-modules' ); ?>  &#8616;</th>
<th style="cursor:pointer;"><?php echo __( 'Name', 'ada2go-text-modules' ); ?>  &#8616;</th>
<th style="cursor:pointer;"><?php echo __( 'Beschreibung', 'ada2go-text-modules' ); ?>  &#8616;</th>
<th style="cursor:pointer;"><?php echo __( 'Sortierung', 'ada2go-text-modules' ); ?>  &#8616;</th>
</tr>
<?php echo html_entity_decode ( a2g_tm_while_categorys() ); ?>
</table>
</div>
</div>

<div id="katAdd" class="tabcontent">
  <h3>Kategorien hinzuf&uuml;gen</h3>
  <div class="a2g-p-container">
<form action="?page=a2g_tm_settings&action=addcat" method="post">
<label for="cat-name"><?php echo __( 'Kategorie Name', 'ada2go-text-modules' ); ?></label><br />
<input type="text" id="cat-name" name="cat-name" autofocus required>
<br /><br />
<?php echo __( 'Beschreibung', 'ada2go-text-modules' ); ?><br />
<?php 
echo wp_editor( '', 'kat-desc'); ?>
<br />
<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( 'Kategorie speichern', 'ada2go-text-modules' ); ?>">
</p>
</form>
</div>
</div>

<div id="textEdit" class="tabcontent">
  <h3>Texte bearbeiten</h3>
<div class="a2g-p-container">
<table id="a2g-table">
<tr>
<th style="width:4%;cursor:pointer;"><?php echo __( 'ID', 'ada2go-text-modules' ); ?> &#8616;</th>
<th style="cursor:pointer;"><?php echo __( 'Name', 'ada2go-text-modules' ); ?> &#8616;</th>
<th style="cursor:pointer;"><?php echo __( 'Kategorie', 'ada2go-text-modules' ); ?> &#8616;</th>
</tr>
<?php echo html_entity_decode (a2g_tm_while_textes()); ?>
</table>
</div>
</div>

<div id="textAdd" class="tabcontent">
  <h3>Texte hinzuf&uuml;gen</h3>
<form action="?page=a2g_tm_settings&action=addtext" method="post">
<div class="a2g-p-container">
<label for="text-ident"><?php echo __( 'Modul Name', 'ada2go-text-modules' ); ?></label><br />
<input type="text" id="text-ident" name="text-ident" autofocus required>
<br />
<?php echo __( 'Text', 'ada2go-text-modules' ); ?><br />
<?php 
echo wp_editor( '', 'text-desc'); ?>
    </div>
<br />
<label for="option-kat" required><?php echo __( 'Zu welcher Kategorie soll der Text gespeichert werden?', 'ada2go-text-modules' ); ?></label><br />
<?php echo $a2g_out_kat; ?>
<br />
<br />
<input<?php if($a2g_tm_kat_exist=false) echo " disabled"; ?> type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __( 'Text speichern', 'ada2go-text-modules' ); ?>">
</form>
</div>

<div id="textSafes" class="tabcontent">
  <h3>Gespeicherte Ausgaben</h3>
<div class="a2g-p-container">
<table id="a2g-table">
<tr>
<th><?php echo __( 'Dateiname', 'ada2go-text-modules' ); ?></th>
</tr>
<?php
echo a2g_tm_while_safed_textes();
?>
</table>
</div>
</div>

<br /><br />
<hr>
<h2><?php echo esc_html_e('Passe dein CSS an', 'ada2go-text-modules'); ?></h2>
<p>
	<?php echo esc_html_e('Um dein Design anzupassen, kannst du folgende Klassen verwenden:', 'ada2go-text-modules'); ?><br><br>
  <b>.a2g-p-container</b> <?php echo esc_html_e('Diese Hilfs-Klasse sorgt f&uuml;r den Abstand im Tab-Content.', 'ada2go-text-modules'); ?><br>
  <b>.tabcontent</b> <?php echo esc_html_e('Diese Klasse ist f&uuml;r den Tab-Content zust&auml;ndig.', 'ada2go-text-modules'); ?><br>
  <b>.tab</b> <?php echo esc_html_e('Diese Klasse ist f&uuml;r einzelne Tabs zust&auml;ndig.', 'ada2go-text-modules'); ?><br>
  <b>#a2g-table</b> <?php echo esc_html_e('Diese ID ist f&uuml;r die Tabelle zust&auml;ndig.', 'ada2go-text-modules'); ?><br>
  <br><br><a href="<?php echo esc_url( get_site_url().'/wp-admin/customize.php' ); ?>" target="_blank">
  <input type="submit" value="<?php esc_html_e( '&Ouml;ffne den Customizer in einem neuen Fenster', 'ada2go-text-modules' ); ?>" class="button"></a> 
</p>

<?php 
}
?>
<script>
function a2gSettingTab(evt, a2gSettingName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(a2gSettingName).style.display = "block";
  evt.currentTarget.className += " active";
}



const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
    v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
    )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
// do the work...
document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
    const table = th.closest('table');
    Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
        .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
        .forEach(tr => table.appendChild(tr) );
})));
</script>